<!--sidebar end-->

<!-- ****************************************************
	  MAIN CONTENT
	  ***************************************************** -->
<!--main content start-->
<?php
$id = $_SESSION['admin']['id_member'];
$hasil = $lihat->member_edit($id);
?>
<h4>Keranjang Penjualan</h4>
<br>
<?php if (isset($_GET['success'])) { ?>
	<div class="alert alert-success">
		<p>Edit Data Berhasil !</p>
	</div>
<?php } ?>
<?php if (isset($_GET['remove'])) { ?>
	<div class="alert alert-danger">
		<p>Hapus Data Berhasil !</p>
	</div>
<?php } ?>
<div class="row">
	<div class="col-sm-4">
		<div class="card card-primary mb-3">
			<div class="card-header bg-primary text-white">
				<h5><i class="fa fa-search"></i> Cari Barang</h5>
			</div>
			<div class="card-body">
				<input type="text" id="cari" class="form-control" name="cari"
					placeholder="Masukan : Kode / Nama Barang  [ENTER]">
			</div>
		</div>
	</div>
	<div class="col-sm-8">
		<div class="card card-primary mb-3">
			<div class="card-header bg-primary text-white">
				<h5><i class="fa fa-list"></i> Hasil Pencarian</h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<div id="hasil_cari"></div>
					<div id="tunggu"></div>
				</div>
			</div>
		</div>
	</div>


	<div class="col-sm-12">
		<div class="card card-primary">
			<div class="card-header bg-primary text-white">
				<h5><i class="fa fa-shopping-cart"></i> KASIR
					<button class="btn btn-danger float-right" data-toggle="modal" data-target="#resetModal">
						<b>RESET KERANJANG</b>
					</button>
				</h5>
			</div>
			<div class="card-body">
				<!-- Tambahkan form baru untuk menambah penjualan -->
				<form id="penjualanForm" method="POST" action="fungsi/tambah/tambah.php?penjualan=tambah">
					<div class="form-row">
						<div class="form-group col-md-3">
							<label for="nama_barang">Nama Barang</label>
							<select class="form-control" id="nama_barang" name="nama_barang" required>
								<option value="">Pilih Barang</option>
								<?php
								$sql_barang = "SELECT id_barang, nama_barang, stok FROM barang ORDER BY nama_barang ASC";
								$row_barang = $config->prepare($sql_barang);
								$row_barang->execute();
								while ($barang = $row_barang->fetch(PDO::FETCH_ASSOC)) {
									echo "<option value='" . $barang['id_barang'] . "' data-stok='" . $barang['stok'] . "'>" . $barang['nama_barang'] . "</option>";
								}
								?>
							</select>
						</div>
						<div class="form-group col-md-3">
							<label for="stok_akhir">Stok Akhir</label>
							<input type="text" class="form-control" id="stok_akhir" name="stok_akhir" readonly>
						</div>
						<div class="form-group col-md-3">
							<label for="jumlah">Jumlah</label>
							<input type="number" class="form-control" id="jumlah" name="jumlah" required>
						</div>
						<div class="form-group col-md-3">
							<label>&nbsp;</label>
							<button type="submit" class="btn btn-success btn-block">Tambah Penjualan</button>
						</div>
					</div>
				</form>

				<!-- Modal -->
				<div class="modal fade" id="stokModal" tabindex="-1" role="dialog" aria-labelledby="stokModalLabel"
					aria-hidden="true">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="stokModalLabel">Peringatan</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								Jumlah melebihi stok akhir!
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
							</div>
						</div>
					</div>
				</div>

				<script>
					document.getElementById('nama_barang').addEventListener('change', function () {
						var stok = this.options[this.selectedIndex].getAttribute('data-stok');
						document.getElementById('stok_akhir').value = stok;
					});

					document.getElementById('penjualanForm').addEventListener('submit', function (event) {
						var stokAkhir = parseInt(document.getElementById('stok_akhir').value);
						var jumlah = parseInt(document.getElementById('jumlah').value);

						if (jumlah > stokAkhir) {
							event.preventDefault(); // Mencegah form dikirim
							$('#stokModal').modal('show'); // Menampilkan modal Bootstrap
						}
					});
				</script>
				<!-- Akhir form baru -->
				<div class="card-body">
					<div id="keranjang" class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<td><b>Tanggal</b></td>
								<td><input type="text" readonly="readonly" class="form-control"
										value="<?php echo date("j F Y, G:i"); ?>" name="tgl"></td>
							</tr>
							<td>Kode Transaksi</td>
							<td colspan="1">
								<?php
								$tanggal = date('dmY');
								$sql_kode = "SELECT MAX(kode_transaksi) AS max_kode FROM nota WHERE kode_transaksi LIKE 'TRX-$tanggal-%'";
								$result_kode = $config->prepare($sql_kode);
								$result_kode->execute();
								$data_kode = $result_kode->fetch(PDO::FETCH_ASSOC);
								$max_kode = $data_kode['max_kode'];
								$no_urut = (int) substr($max_kode, -3);
								$no_urut++;
								$kode_transaksi = "TRX-$tanggal-" . sprintf("%03s", $no_urut);
								?>
								<input type="text" class="form-control" name="kode_transaksi"
									value="<?php echo $kode_transaksi; ?>" readonly>
							</td>
							</tr>
						</table>
						<table class="table table-bordered w-100" id="example1">
							<thead>
								<tr>
									<td> No</td>
									<td> Nama Barang</td>
									<td style="width:10%;"> Jumlah</td>
									<td style="width:10%;"> Stok Akhir</td>
									<td style="width:20%;"> Total</td>
									<td> Kasir</td>
									<td> Aksi</td>
								</tr>
							</thead>
							<tbody>
								<?php $total_bayar = 0;
								$no = 1;
								$hasil_penjualan = $lihat->penjualan(); ?>
								<?php foreach ($hasil_penjualan as $isi) { ?>
									<tr>
										<td><?php echo $no; ?></td>
										<td><?php echo $isi['nama_barang']; ?></td>
										<td>
											<!-- aksi ke table penjualan -->
											<form method="POST" action="fungsi/edit/edit.php?jual=jual">
												<input type="number" name="jumlah" value="<?php echo $isi['jumlah']; ?>"
													class="form-control">
												<input type="hidden" name="id" value="<?php echo $isi['id_penjualan']; ?>"
													class="form-control">
												<input type="hidden" name="id_barang"
													value="<?php echo $isi['id_barang']; ?>" class="form-control">
										</td>
										<td style="width:10%;">
											<?php
											// Panggil stok akhir dari database
											$sql_stok = "SELECT stok FROM barang WHERE id_barang = ?";
											$row_stok = $config->prepare($sql_stok);
											$row_stok->execute(array($isi['id_barang']));
											$stok_akhir = $row_stok->fetch(PDO::FETCH_ASSOC)['stok'];
											echo $stok_akhir;
											?>
										</td>
										<td>Rp.<?php echo number_format($isi['total']); ?>,-</td>
										<td><?php echo $isi['nm_member']; ?></td>
										<td>
											<button type="submit" class="btn btn-warning">Update Jumlah</button>
											</form>
											<!-- aksi ke table penjualan -->
											<a href="fungsi/hapus/hapus.php?jual=jual&id=<?php echo $isi['id_penjualan']; ?>&brg=<?php echo $isi['id_barang']; ?>
											&jml=<?php echo $isi['jumlah']; ?>" class="btn btn-danger"><i class="fa fa-times"></i>
											</a>
										</td>
									</tr>
									<?php $no++;
									$total_bayar += $isi['total'];
								} ?>
							</tbody>
						</table>
						<br />
						<?php $hasil = $lihat->jumlah(); ?>
						<div id="kasirnya">
							<table class="table table-stripped">
								<?php
								// proses bayar dan ke nota
								if (!empty($_GET['nota'] == 'yes')) {
									$total = $_POST['total'];
									$bayar = $_POST['bayar'];
									$kode_transaksi = $_POST['kode_transaksi']; // Ambil kode transaksi dari form
									if (!empty($bayar)) {
										$hitung = $bayar - $total;
										if ($bayar >= $total) {
											$id_barang = $_POST['id_barang'];
											$id_member = $_POST['id_member'];
											$jumlah = $_POST['jumlah'];
											$total = $_POST['total1'];
											$tgl_input = $_POST['tgl_input'];
											$periode = $_POST['periode'];
											$jumlah_dipilih = count($id_barang);

											for ($x = 0; $x < $jumlah_dipilih; $x++) {

												$d = array($id_barang[$x], $id_member[$x], $jumlah[$x], $total[$x], $tgl_input[$x], $periode[$x], $_POST['metode_pembayaran'], $kode_transaksi);
												$sql = "INSERT INTO nota (id_barang,id_member,jumlah,total,tanggal_input,periode,metode_pembayaran,kode_transaksi) VALUES(?,?,?,?,?,?,?,?)";
												$row = $config->prepare($sql);
												$row->execute($d);

												// ubah stok barang
												$sql_barang = "SELECT * FROM barang WHERE id_barang = ?";
												$row_barang = $config->prepare($sql_barang);
												$row_barang->execute(array($id_barang[$x]));
												$hsl = $row_barang->fetch();

												$stok = $hsl['stok'];
												$idb = $hsl['id_barang'];

												$total_stok = $stok - $jumlah[$x];
												// echo $total_stok;
												$sql_stok = "UPDATE barang SET stok = ? WHERE id_barang = ?";
												$row_stok = $config->prepare($sql_stok);
												$row_stok->execute(array($total_stok, $idb));
											}
											// Simpan kode transaksi ke dalam tabel nota
											$sql_kode_transaksi = "INSERT INTO nota (kode_transaksi) VALUES (?)";
											$row_kode_transaksi = $config->prepare($sql_kode_transaksi);
											$row_kode_transaksi->execute(array($kode_transaksi));

											echo '<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title" id="successModalLabel">Pembayaran Berhasil</h5>
															</div>
															<div class="modal-body">
																Belanjaan Berhasil Di Bayar!
															</div>
															<div class="modal-footer">
																<a href="print.php?nm_member=' . $_SESSION['admin']['nm_member'] . '&bayar=' . $bayar . '&kembali=' . $hitung . '&total=' . $total_bayar . '" target="_blank" class="btn btn-secondary" id="printButton">
																	<i class="fa fa-print"></i> Print Untuk Bukti Pembayaran
																</a>
															</div>
														</div>
													</div>
												</div>';
											echo '<script>
													$(document).ready(function(){
														$("#successModal").modal("show");

														$("#printButton").on("click", function() {
															window.open($(this).attr("href"), "_blank");
															window.location.href = "fungsi/hapus/hapus.php?penjualan=jual";
														});
													});
												</script>';
										} else {
											echo '<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title" id="errorModalLabel">Pembayaran Gagal</h5>
																<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																	<span aria-hidden="true">&times;</span>
																</button>
															</div>
															<div class="modal-body">
																Uang Kurang! Rp.' . $hitung . '
															</div>
															<div class="modal-footer">
																<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
															</div>
														</div>
													</div>
												</div>';
											echo '<script>
													$(document).ready(function(){
														$("#errorModal").modal("show");
													});
												</script>';
										}
									}
								}
								?>
								<!-- aksi ke table nota -->
								<form method="POST" action="index.php?page=jual&nota=yes#kasirnya">
									<?php foreach ($hasil_penjualan as $isi) {
										; ?>
										<input type="hidden" name="id_barang[]" value="<?php echo $isi['id_barang']; ?>">
										<input type="hidden" name="id_member[]" value="<?php echo $isi['id_member']; ?>">
										<input type="hidden" name="jumlah[]" value="<?php echo $isi['jumlah']; ?>">
										<input type="hidden" name="total1[]" value="<?php echo $isi['total']; ?>">
										<input type="hidden" name="tgl_input[]"
											value="<?php echo $isi['tanggal_input']; ?>">
										<input type="hidden" name="periode[]" value="<?php echo date('m-Y'); ?>">
										<?php $no++;
									} ?>
									<input type="hidden" name="kode_transaksi" value="<?php echo $kode_transaksi; ?>">


									<tr>
										<td>Metode Pembayaran</td>
										<td colspan="1">
											<select class="form-control" name="metode_pembayaran" required>
												<option value="">Pilih Metode Pembayaran</option>
												<option value="Cash">Cash</option>
												<option value="Kode QR">QR Kode</option>
											</select>
										</td>
									</tr>
									<tr>
										<td>Total Semua </td>
										<td><input type="text" class="form-control" name="total"
												value="<?php echo $total_bayar; ?>" readonly></td>

										<td>Bayar </td>
										<td>
											<input type="number" class="form-control" name="bayar"
												value="<?php echo $bayar; ?>" required>
										</td>
										<td>
											<button class="btn btn-success" id="btnBayar" disabled><i
													class="fa fa-shopping-cart"></i> Bayar</button>
										</td>
									</tr>
								</form>
								<!-- aksi ke table nota -->
								<tr>
									<td>Kembalian</td>
									<td><input type="text" class="form-control" id="kembalian" readonly></td>
									<td></td>
									<td>
										<a href="print.php?nm_member=<?php echo $_SESSION['admin']['nm_member']; ?>
									&bayar=<?php echo $bayar; ?>&kembali=<?php echo $hitung; ?>&total=<?php echo $total_bayar; ?>"
											target="_blank">

									</td>
								</tr>
							</table>
							<br />
							<br />
						</div>
					</div>
				</div>
			</div>
		</div>


		<script>
			// AJAX call for autocomplete 
			$(document).ready(function () {
				$("#cari").change(function () {
					$.ajax({
						type: "POST",
						url: "fungsi/edit/edit.php?cari_barang=yes",
						data: 'keyword=' + $(this).val(),
						beforeSend: function () {
							$("#hasil_cari").hide();
							$("#tunggu").html('<p style="color:green"><blink>tunggu sebentar</blink></p>');
						},
						success: function (html) {
							$("#tunggu").html('');
							$("#hasil_cari").show();
							$("#hasil_cari").html(html);
						}
					});
				});
			});
			//To select country name
		</script>

		<script>
			// ... existing code ...
			$('select[name="metode_pembayaran"]').on('change', function () {
				var metode = $(this).val();
				var totalValue = $('input[name="total"]').val();
				if (metode === 'Kode QR') {
					$('input[name="bayar"]').val(totalValue).prop('readonly', true);
					$('#kembalian').val(0);
					$('#btnBayar').prop('disabled', false);
				} else {
					$('input[name="bayar"]').val('').prop('readonly', false);
					$('#kembalian').val('');
					$('#btnBayar').prop('disabled', true);
					if (metode === 'Cash') { // Tambahkan kondisi ini
						$('input[name="bayar"]').focus(); // Fokus ke input bayar
					}
				}
			});
			// ... existing code ...	
			$(document).ready(function () {
				// Mengaktifkan/menonaktifkan tombol bayar berdasarkan input bayar
				$('input[name="bayar"]').on('input', function () {
					var bayarValue = $(this).val();
					var totalValue = $('input[name="total"]').val();
					var kembalian = bayarValue - totalValue;
					$('#kembalian').val(kembalian);
					$('#btnBayar').prop('disabled', !bayarValue); // Nonaktifkan jika kosong
				});

				// Event listener untuk metode pembayaran
				$('select[name="metode_pembayaran"]').on('change', function () {
					var metode = $(this).val();
					var totalValue = $('input[name="total"]').val();
					if (metode === 'Kode QR') {
						$('input[name="bayar"]').val(totalValue).prop('readonly', true);
						$('#kembalian').val(0);
						$('#btnBayar').prop('disabled', false);
					} else {
						$('input[name="bayar"]').val('').prop('readonly', false);
						$('#kembalian').val('');
						$('#btnBayar').prop('disabled', true);
					}
				});
			});
		</script>

		<!-- Modal -->
		<div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-labelledby="resetModalLabel"
			aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="resetModalLabel">Konfirmasi Reset Keranjang</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						Apakah anda ingin reset keranjang?
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
						<a class="btn btn-danger" href="fungsi/hapus/hapus.php?penjualan=jual">Reset Keranjang</a>
					</div>
				</div>
			</div>
		</div>