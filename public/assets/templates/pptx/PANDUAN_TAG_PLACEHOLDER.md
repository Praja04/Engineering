# 📘 Panduan Tag & Placeholder PPTX Weekly Utility

File ini berisi daftar lengkap tag placeholder yang dapat digunakan di dalam master template PowerPoint:
`public/assets/templates/pptx/operasional_utility.pptx`

Setiap tag yang ditulis dengan format kurung kurawal ganda `{{nama_tag}}` di dalam file PowerPoint akan **otomatis digantikan dengan nilai aktual dari sistem database Engineering** saat diunduh melalui menu **PPTX Weekly Utility**.

---

## 🚀 Cara Penggunaan di Microsoft PowerPoint

1. Buka file template:
   📂 `public/assets/templates/pptx/operasional_utility.pptx`
2. Pilih kotak teks (*Text Box*), bentuk (*Shape*), atau sel tabel (*Table Cell*) yang ingin dijadikan dinamis.
3. Hapus teks angka/tanggal statis lama, lalu ketik tag yang sesuai, contoh:
   * **Sebelum:** `Periode Agenda 12 - 18 Oktober 2026`
   * **Sesudah:** `{{periode_agenda}}`
4. Format font, warna, ukuran, dan perataan (*alignment*) tetap diatur bebas di PowerPoint seperti biasa.
5. Tekan **Save / Ctrl + S**.
6. Buka web Engineering $\rightarrow$ Klik menu **Dashboard Utility & Boiler** $\rightarrow$ **PPTX Weekly Utility**. File akan terunduh otomatis dengan data terisi!

---

## 📋 Daftar Lengkap Tag / Placeholder Siap Pakai

### 1. Informasi Umum & Periode Agenda (Cover Slide 1 & Header)
| Tag Placeholder | Deskripsi | Contoh Nilai Otomatis |
| :--- | :--- | :--- |
| `{{periode_agenda}}` | Teks lengkap periode agenda mingguan | `Periode Agenda 12 - 18 Oktober 2026` |
| `{{periode}}` | Rentang tanggal awal s/d akhir | `12 - 18 Oktober 2026` |
| `{{tanggal}}` | Tanggal hari terakhir periode laporan | `18 Oktober 2026` |
| `{{bulan}}` | Nama bulan dan tahun | `Oktober 2026` |
| `{{minggu}}` | Nomor minggu dalam tahun berjalan | `W42` |
| `{{tahun}}` | Tahun 4 digit | `2026` |
| `{{mode}}` | Mode filter aktif | `Mingguan` |
| `{{last_update}}` | Timestamp pembuatan file PPTX | `22 Oct 2026 15:45:00` |

---

### 2. Mesin Utility & Air Compressor (Slide 8 & Slide Terkait)
| Tag Placeholder | Deskripsi | Contoh Nilai Otomatis |
| :--- | :--- | :--- |
| `{{compressor.ga37}}` / `{{compressor.rh1}}` | Running hours mingguan Compressor GA37 | `168 Jam` |
| `{{compressor.ga57}}` / `{{compressor.rh2}}` | Running hours mingguan Compressor GA57 | `168 Jam` |
| `{{compressor.ir55}}` / `{{compressor.rh3}}` | Running hours mingguan Compressor IR55 | `168 Jam` |
| `{{compressor.ga55}}` / `{{compressor.rh4}}` | Running hours mingguan Compressor GA55 | `168 Jam` |
| `{{compressor.rh1_avg}}` | Rata-rata jam operasi Compressor GA37 | `24.0 Jam/Hari` |
| `{{compressor.ga57_avg}}` | Rata-rata jam operasi Compressor GA57 | `24.0 Jam/Hari` |
| `{{compressor.ir55_avg}}` | Rata-rata jam operasi Compressor IR55 | `24.0 Jam/Hari` |
| `{{compressor.ga55_avg}}` | Rata-rata jam operasi Compressor GA55 | `24.0 Jam/Hari` |

---

### 3. AHU & Tata Udara
| Tag Placeholder | Deskripsi | Contoh Nilai Otomatis |
| :--- | :--- | :--- |
| `{{ahu.avg_temp}}` | Rata-rata suhu ruang AHU | `21.4 °C` |
| `{{ahu.avg_rh}}` | Rata-rata kelembaban relatif (RH) AHU | `58.2 %` |

---

### 4. Cooling Tower & Genset
| Tag Placeholder | Deskripsi | Contoh Nilai Otomatis |
| :--- | :--- | :--- |
| `{{cooling_tower.temp_out}}` | Temperatur air keluar Cooling Tower | `29.5 °C` |
| `{{cooling_tower.pressure}}` | Tekanan pompa Cooling Tower | `2.8 Bar` |
| `{{genset.rh_total}}` | Total running hours warming up Genset | `12.5 Jam` |

---

### 5. Water Treatment: Reverse Osmosis (RO) & Water Softener (WS) (Slide 8)
| Tag Placeholder | Deskripsi | Contoh Nilai Otomatis |
| :--- | :--- | :--- |
| `{{water.ro_tds}}` | Rata-rata TDS Permeate RO | `45 ppm` |
| `{{water.ws_hardness}}` | Rata-rata Hardness Water Softener | `0.2 ppm` |
| `{{water.usage_total}}` | Total konsumsi pemakaian air | `1.420,5 m³` |

---

### 6. Power House & Kelistrikan / MDP
| Tag Placeholder | Deskripsi | Contoh Nilai Otomatis |
| :--- | :--- | :--- |
| `{{mdp.total_energy}}` | Total energi listrik terkonsumsi | `125.400 kWh` |
| `{{powerhouse.pln}}` | Total konsumsi listrik dari PLN | `98.200 kWh` |
| `{{powerhouse.plts}}` | Total konsumsi listrik dari PLTS Solar | `27.200 kWh` |
| `{{powerhouse.invoice}}` | Estimasi tagihan/biaya listrik | `Rp 145.200.000` |

---

### 7. Biaya & SMKP Engineering Utility (Slide 2 & Slide 18)
| Tag Placeholder | Deskripsi | Contoh Nilai Otomatis |
| :--- | :--- | :--- |
| `{{cost.bb_fg}}` | Rasio pemakaian Batubara per Ton FG | `48.50 Kg/Ton FG` |
| `{{cost.listrik_km}}` | Rasio pemakaian Listrik per Ton KM | `112.40 kWh/Ton KM` |
| `{{cost.total_cost}}` | Total biaya operasional Utility | `Rp 340,5 Juta` |

---

## 💡 Penanganan Slide yang Belum Ada Datanya di Web
*(Contoh: Slide 9 Foto Batubara, Slide 10 Verifikasi Delta P Steam, Slide 11 Action Plan Boiler, Slide 14/16 Dokumentasi Perbaikan Mesin)*

1. **Slide Manual Tetap Aman:**
   Sistem **hanya mengganti teks yang berformat `{{...}}`**. Slide, gambar, foto, tabel, dan teks manual yang tidak menggunakan tanda kurung kurawal ganda akan **tetap utuh dan tidak terhapus**.
2. **Fleksibilitas:**
   Setelah file hasil download dibuka, tim Utility bisa langsung menyisipkan foto dokumentasi mingguan atau menambahkan catatan khusus meeting di PowerPoint sebelum dipresentasikan.
