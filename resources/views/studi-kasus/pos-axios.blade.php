@extends('layouts.app')

@section('title', 'Studi Kasus POS - Axios')

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">POS (Axios)</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('studi-kasus.pos-jquery') }}" class="btn btn-outline-primary btn-sm">jQuery</a>
                        <a href="{{ route('studi-kasus.pos-axios') }}" class="btn btn-primary btn-sm">Axios</a>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Kode barang (tekan Enter)</label>
                        <input type="text" id="kodeBarang" class="form-control" placeholder="Contoh: 26040101">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama barang</label>
                        <input type="text" id="namaBarang" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Harga barang</label>
                        <input type="number" id="hargaBarang" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jumlah</label>
                        <input type="number" id="jumlahBarang" class="form-control" min="1" value="1">
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-3">
                    <button type="button" id="btnTambah" class="btn btn-success" disabled>
                        <span class="label">Tambahkan</span>
                    </button>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered" id="tabelPos">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Total: <span id="grandTotal">Rp 0</span></h4>
                    <button type="button" id="btnBayar" class="btn btn-primary" disabled>
                        <span class="label">Bayar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (function () {
        const kode = document.getElementById('kodeBarang');
        const nama = document.getElementById('namaBarang');
        const harga = document.getElementById('hargaBarang');
        const jumlah = document.getElementById('jumlahBarang');
        const tbody = document.querySelector('#tabelPos tbody');
        const grandTotal = document.getElementById('grandTotal');

        const btnTambah = document.getElementById('btnTambah');
        const btnBayar = document.getElementById('btnBayar');

        let barangAktif = null;
        const cart = [];

        function rupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        function setButtonLoading(button, loading, fallbackText) {
            const label = button.querySelector('.label');
            if (!label) return;

            if (loading) {
                button.disabled = true;
                label.dataset.original = label.textContent;
                label.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
            } else {
                label.textContent = label.dataset.original || fallbackText;
            }
        }

        function syncButtons() {
            btnTambah.disabled = !(barangAktif && Number(jumlah.value) > 0);
            btnBayar.disabled = cart.length === 0;
        }

        function resetInputBarang() {
            barangAktif = null;
            kode.value = '';
            nama.value = '';
            harga.value = '';
            jumlah.value = 1;
            syncButtons();
            kode.focus();
        }

        function recalcTotal() {
            const total = cart.reduce(function (sum, item) {
                return sum + (item.harga * item.jumlah);
            }, 0);
            grandTotal.textContent = rupiah(total);
            btnBayar.disabled = cart.length === 0;
        }

        function renderTable() {
            tbody.innerHTML = '';

            cart.forEach(function (item, idx) {
                const tr = document.createElement('tr');
                tr.dataset.index = idx;
                tr.innerHTML = `
                    <td>${item.kode}</td>
                    <td>${item.nama}</td>
                    <td>${rupiah(item.harga)}</td>
                    <td><input type="number" class="form-control form-control-sm qty-input" min="1" value="${item.jumlah}"></td>
                    <td>${rupiah(item.harga * item.jumlah)}</td>
                    <td><button type="button" class="btn btn-danger btn-sm btn-hapus">X</button></td>
                `;
                tbody.appendChild(tr);
            });

            recalcTotal();
        }

        kode.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter') {
                return;
            }

            e.preventDefault();
            const kodeVal = kode.value.trim();
            if (!kodeVal) {
                return;
            }

            axios.get('/studi-kasus/api/pos/barang/' + kodeVal)
                .then(function (res) {
                    barangAktif = {
                        kode: res.data.data.id_barang,
                        nama: res.data.data.nama,
                        harga: Number(res.data.data.harga)
                    };

                    nama.value = barangAktif.nama;
                    harga.value = barangAktif.harga;
                    jumlah.value = 1;
                    syncButtons();
                })
                .catch(function () {
                    barangAktif = null;
                    nama.value = '';
                    harga.value = '';
                    syncButtons();
                    Swal.fire('Error', 'Barang tidak ditemukan', 'error');
                });
        });

        jumlah.addEventListener('input', syncButtons);

        btnTambah.addEventListener('click', function () {
            if (!barangAktif || Number(jumlah.value) <= 0) {
                return;
            }

            setButtonLoading(btnTambah, true, 'Tambahkan');
            setTimeout(function () {
                const qty = Number(jumlah.value);
                const existing = cart.find(function (x) { return x.kode === barangAktif.kode; });

                if (existing) {
                    existing.jumlah += qty;
                } else {
                    cart.push({
                        kode: barangAktif.kode,
                        nama: barangAktif.nama,
                        harga: barangAktif.harga,
                        jumlah: qty
                    });
                }

                renderTable();
                resetInputBarang();
                setButtonLoading(btnTambah, false, 'Tambahkan');
            }, 350);
        });

        tbody.addEventListener('input', function (e) {
            if (!e.target.classList.contains('qty-input')) {
                return;
            }

            const tr = e.target.closest('tr');
            const idx = Number(tr.dataset.index);
            const qty = Number(e.target.value);
            if (!cart[idx] || qty <= 0) {
                return;
            }

            cart[idx].jumlah = qty;
            renderTable();
        });

        tbody.addEventListener('click', function (e) {
            if (!e.target.classList.contains('btn-hapus')) {
                return;
            }

            const tr = e.target.closest('tr');
            const idx = Number(tr.dataset.index);
            if (!cart[idx]) {
                return;
            }

            cart.splice(idx, 1);
            renderTable();
            syncButtons();
        });

        btnBayar.addEventListener('click', function () {
            if (cart.length === 0) {
                return;
            }

            const payload = {
                items: cart.map(function (item) {
                    return { kode: item.kode, jumlah: item.jumlah };
                })
            };

            setButtonLoading(btnBayar, true, 'Bayar');

            axios.post('{{ route('studi-kasus.api.pos.checkout') }}', payload, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(function (res) {
                Swal.fire('Sukses', res.data.message, 'success');
                cart.splice(0, cart.length);
                renderTable();
                resetInputBarang();
            })
            .catch(function () {
                Swal.fire('Error', 'Gagal menyimpan transaksi', 'error');
            })
            .finally(function () {
                setButtonLoading(btnBayar, false, 'Bayar');
                syncButtons();
            });
        });
    })();
</script>
@endpush
