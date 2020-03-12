=== Plugin Name ===
Contributors: ridwanarifandi
Donate link: https://ridwan-arifandi.com
Tags: license
Requires at least: 5.3.0
Tested up to: 5.3.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simulasi penggunaan lisensi melalu sejoli

== Description ==

Ada 2 endpoint yang berfungsi mengatur lisensi pada sejoli

1. Pendaftaran Lisensi

URL     : {{weburl}}/sejoli/sejoli-license/
TYPE    : POST
DATA    :
- (string) $username    Username customer di membership sejoli
- (string) $password    Password customer di membership sejoli
- (string) $license     Lisensi yang didapatkan
- (string) $string      Penanda, harus unik, bisa gunakan nama domain, IP address dll

Setelah status order menjadi SELESAI/COMPLETE, customer akan mendapatkan lisensi.
Gunakan endpoint ini untuk mendaftarkan variable string ke membership anda.
Jika status pendaftaran lisensi valid, sebaiknya di aplikasi teman-teman, data lisensi tersebut disimpan.

2. Pengecekan Lisensi

URL     : {{weburl}}/sejoli/sejoli-validate-license/
TYPE    : GET
DATA    :
- (string)  $license    Lisensi yang didapatkan
- (string)  $string     Penanda, harus unik, bisa gunakan nama domain, IP address dll

Kami mengajurkan rutin pengecekan lisensi ini berjalan di background sistem, contohnya melalui CRON JOB.
