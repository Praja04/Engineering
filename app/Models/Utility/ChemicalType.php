<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemicalType extends Model
{
    use HasFactory;

    protected $table = 'chemical_types';
    protected $primaryKey = 'id';
    protected $fillable = [
        'chemical_area_id',
        'nama_chemical',
        'satuan',
        'is_active',
        'tipe_perhitungan',
        'rumus_formula',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function area()
    {
        return $this->belongsTo(ChemicalArea::class, 'chemical_area_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('nama_chemical');
    }

    /**
     * Hitung total pemakaian berdasarkan nilai dan running hour.
     * Menggunakan rumus jika tipe_perhitungan == 'rumus', atau langsung nilai.
     */
    public function calculateUsage(float $nilai, float $rh = 1.0): float
    {
        if ($this->tipe_perhitungan === 'rumus' && !empty($this->rumus_formula)) {
            return self::evaluateFormula($this->rumus_formula, $nilai, $rh);
        }

        return $nilai;
    }

    /**
     * Alias bahasa Indonesia untuk calculateUsage
     */
    public function hitungPemakaian(float $nilai, float $rh = 1.0): float
    {
        return $this->calculateUsage($nilai, $rh);
    }

    /**
     * Evaluasi formula matematika secara aman tanpa eval()
     * Format variabel yang didukung: {nilai}, nilai, {rh}, rh
     */
    public static function evaluateFormula(?string $formula, float $nilai, float $rh = 1.0): float
    {
        if (empty($formula)) {
            return $nilai;
        }

        // Ganti variabel dengan angka riil
        $expr = str_ireplace(
            ['{nilai}', '$nilai', '{rh}', '$rh'],
            [(string) $nilai, (string) $nilai, (string) $rh, (string) $rh],
            $formula
        );

        // Ganti kata 'nilai' dan 'rh' yang berdiri sendiri
        $expr = preg_replace('/\bnilai\b/i', (string) $nilai, $expr);
        $expr = preg_replace('/\brh\b/i', (string) $rh, $expr);

        return self::evaluateMath($expr, $nilai);
    }

    /**
     * Safe arithmetic math evaluator (Shunting-yard algorithm + RPN)
     */
    public static function evaluateMath(string $expr, float $fallback = 0.0): float
    {
        // Bersihkan whitespace
        $expr = preg_replace('/\s+/', '', $expr);
        if ($expr === '') {
            return $fallback;
        }

        // Pastikan hanya karakter angka, operator, dan tanda kurung
        if (!preg_match('/^[0-9+\-*\/().]+$/', $expr)) {
            return $fallback;
        }

        // Tokenisasi: angka desimal atau operator
        preg_match_all('/(\d+(?:\.\d+)?|[+\-*\/()])/', $expr, $matches);
        $tokens = $matches[0] ?? [];
        if (empty($tokens)) {
            return $fallback;
        }

        $outputQueue = [];
        $opStack = [];
        $precedence = ['+' => 1, '-' => 1, '*' => 2, '/' => 2];
        $prevToken = null;

        foreach ($tokens as $token) {
            if (is_numeric($token)) {
                $outputQueue[] = (float) $token;
            } elseif ($token === '(') {
                $opStack[] = $token;
            } elseif ($token === ')') {
                while (!empty($opStack) && end($opStack) !== '(') {
                    $outputQueue[] = array_pop($opStack);
                }
                if (!empty($opStack) && end($opStack) === '(') {
                    array_pop($opStack);
                }
            } elseif (isset($precedence[$token])) {
                // Handle unary minus
                if ($token === '-' && ($prevToken === null || $prevToken === '(' || isset($precedence[$prevToken]))) {
                    $outputQueue[] = 0.0;
                }
                while (
                    !empty($opStack) &&
                    end($opStack) !== '(' &&
                    isset($precedence[end($opStack)]) &&
                    $precedence[end($opStack)] >= $precedence[$token]
                ) {
                    $outputQueue[] = array_pop($opStack);
                }
                $opStack[] = $token;
            }
            $prevToken = $token;
        }

        while (!empty($opStack)) {
            $op = array_pop($opStack);
            if ($op !== '(' && $op !== ')') {
                $outputQueue[] = $op;
            }
        }

        // Evaluasi RPN (Reverse Polish Notation)
        $evalStack = [];
        foreach ($outputQueue as $item) {
            if (is_numeric($item)) {
                $evalStack[] = (float) $item;
            } else {
                if (count($evalStack) < 2) {
                    continue;
                }
                $b = array_pop($evalStack);
                $a = array_pop($evalStack);
                switch ($item) {
                    case '+':
                        $evalStack[] = $a + $b;
                        break;
                    case '-':
                        $evalStack[] = $a - $b;
                        break;
                    case '*':
                        $evalStack[] = $a * $b;
                        break;
                    case '/':
                        $evalStack[] = $b != 0.0 ? ($a / $b) : 0.0;
                        break;
                }
            }
        }

        return !empty($evalStack) ? (float) end($evalStack) : $fallback;
    }
}
