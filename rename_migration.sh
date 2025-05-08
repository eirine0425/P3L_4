#!/bin/bash

# Format penomoran timestamp dimulai dari tanggal hari ini
BASE="2025_05_09"
COUNT=1

# Urutan tabel berdasarkan dependensi relasi (paling aman)
FILES=(
  "create_users_table.php"
  "create_personal_access_tokens_table.php"
  "create_alamats_table.php"
  "create_kategori_barangs_table.php"
  "create_garansis_table.php"
  "create_barang_table.php"
  "create_merches_table.php"
  "create_pegawais_table.php"
  "create_penitips_table.php"
  "create_pembelis_table.php"
  "create_keranjang_belanjas_table.php"
  "create_diskusi_produks_table.php"
  "create_donasis_table.php"
  "create_komisis_table.php"
  "create_organisasis_table.php"
  "create_transaksis_table.php"
  "create_pengiriman_table.php"
  "create_detail_transaksis_table.php"
  "create_transaksi_merches_table.php"
  "create_transaksi_penitipans_table.php"
)

for FILE in "${FILES[@]}"; do
  NEW_FILE=$(printf "%s_%06d_%s" "$BASE" "$COUNT" "$FILE")
  echo "Renaming $FILE -> $NEW_FILE"
  mv "database/migrations/"*$FILE "database/migrations/$NEW_FILE"
  ((COUNT++))
done