@extends('layouts.app')

@section('title', 'Studi Kasus POS - jQuery Ajax')

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">POS (jQuery Ajax)</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('studi-kasus.pos-jquery') }}" class="btn btn-primary btn-sm">jQuery</a>
                        <a href="{{ route('studi-kasus.pos-axios') }}" class="btn btn-outline-primary btn-sm">Axios</a>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function () {
        const $kode = $('#kodeBarang');
        const $nama = $('#namaBarang');
        const $harga = $('#hargaBarang');
        const $jumlah = $('#jumlahBarang');
        const $tbody = $('#tabelPos tbody');

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
            btnTambah.disabled = !(barangAktif && Number($jumlah.val()) > 0);
            btnBayar.disabled = cart.length === 0;
        }

        function resetInputBarang() {
            barangAktif = null;
            $kode.val('');
            $nama.val('');
            $harga.val('');
            $jumlah.val(1);
            syncButtons();
            $kode.focus();
        }

        function recalcTotal() {
            const total = cart.reduce(function (sum, item) {
                return sum + (item.harga * item.jumlah);
            }, 0);
            $('#grandTotal').text(rupiah(total));
            btnBayar.disabled = cart.length === 0;
        }

        function renderTable() {
            $tbody.empty();

            cart.forEach(function (item, idx) {
                const subtotal = item.harga * item.jumlah;
                const row = `
                    <tr data-index="${idx}">
                        <td>${item.kode}</td>
                        <td>${item.nama}</td>
                        <td>${rupiah(item.harga)}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm qty-input" min="1" value="${item.jumlah}">
                        </td>
                        <td>${rupiah(subtotal)}</td>
                        <td>
                            <button class="btn btn-danger btn-sm btn-hapus" type="button">X</button>
                        </td>
                    </tr>
                `;
                $tbody.append(row);
            });

            recalcTotal();
        }

        $kode.on('keydown', function (e) {
            if (e.key !== 'Enter') {
                return;
            }

            e.preventDefault();
            const kode = $kode.val().trim();
            if (!kode) {
                return;
            }

            $.ajax({
                url: '/studi-kasus/api/pos/barang/' + kode,
                type: 'GET',
                success: function (res) {
                    barangAktif = {
                        kode: res.data.id_barang,
                        nama: res.data.nama,
                        harga: Number(res.data.harga)
                    };

                    $nama.val(barangAktif.nama);
                    $harga.val(barangAktif.harga);
                    $jumlah.val(1);
                    syncButtons();
                },
                error: function () {
                    barangAktif = null;
                    $nama.val('');
                    $harga.val('');
                    syncButtons();
                    Swal.fire('Error', 'Barang tidak ditemukan', 'error');
                }
            });
        });

        $jumlah.on('input', function () {
            syncButtons();
        });

        $('#btnTambah').on('click', function () {
            if (!barangAktif || Number($jumlah.val()) <= 0) {
                return;
            }

            setButtonLoading(btnTambah, true, 'Tambahkan');
            setTimeout(function () {
                const qty = Number($jumlah.val());
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

        $tbody.on('input', '.qty-input', function () {
            const tr = $(this).closest('tr');
            const idx = Number(tr.data('index'));
            const qty = Number($(this).val());

            if (!cart[idx] || qty <= 0) {
                return;
            }

            cart[idx].jumlah = qty;
            renderTable();
        });

        $tbody.on('click', '.btn-hapus', function () {
            const idx = Number($(this).closest('tr').data('index'));
            if (!cart[idx]) {
                return;
            }

            cart.splice(idx, 1);
            renderTable();
            syncButtons();
        });

        $('#btnBayar').on('click', function () {
            if (cart.length === 0) {
                return;
            }

            const payload = {
                items: cart.map(function (item) {
                    return { kode: item.kode, jumlah: item.jumlah };
                })
            };

            setButtonLoading(btnBayar, true, 'Bayar');

            $.ajax({
                url: '{{ route('studi-kasus.api.pos.checkout') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    items: payload.items
                },
                success: function (res) {
                    Swal.fire('Sukses', res.message, 'success');
                    cart.splice(0, cart.length);
                    renderTable();
                    resetInputBarang();
                },
                error: function () {
                    Swal.fire('Error', 'Gagal menyimpan transaksi', 'error');
                },
                complete: function () {
                    setButtonLoading(btnBayar, false, 'Bayar');
                    syncButtons();
                }
            });
        });
    });
</script>
@endpush
