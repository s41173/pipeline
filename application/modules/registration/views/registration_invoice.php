<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $h2title; ?></title>
    <style>
        body{
            margin: 10px;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        }
        .wrap{
            box-sizing: border-box;
            border: 3px solid #000;
            padding: 10px;
        }
        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .table, th, td {
            border: 2px solid black;
            align-items: center;
            text-align: center;
        }
        td{
            padding: 5px;
        }
        .flex-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .no-wrap {
            white-space: nowrap;
            word-wrap: break-word;
        }


    </style>
</head>
<body>
    <div class="wrap">
        <div>
            <table class="table">
                <tr>
                    <td>
                        <div class="flex-container">
                            <img width="100px" src="https://inl.co.id/wp-content/uploads/2021/06/LOGO-INL-768x236.png" alt="" srcset="">
                            <h3>PT. INDUSTRI NABATI LESTARI PABRIK MINYAK GORENG</h3>
                        </div>
                    </td>
                    <td class="no-wrap">No. Dok</td>
                    <td class="no-wrap">Tgl. Berlaku</td>
                </tr>
                <tr>
                    <td>Kantor Pusat : Komp.KEK Sei Mangkei, Kav.2-3, Kec. Bosar Maligas, Kab. Simalungun, Sumatera Utara, 21184</td>
                    <td class="no-wrap">No. Revisi</td>
                    <td class="no-wrap">Halaman</td>
                </tr>
                <tr>
                    <td>BERITA ACARA</td>
                    <td class="no-wrap">01</td>
                    <td class="no-wrap">1 dari 1</td>
                </tr>
            </table>
        </div>
        <div style="text-align: center!important;">
            <h3>PENERIMA CPO DI TANGKI PT. INL DARI PKS SEI MANGKEI DAN DRYPORT DENGAN MENGGUNAKAN PIPANISASI</h3>
            <P>No : ...   /BA/TF/ ... /202</P>
        </div>
        <div>
            Pada hari ini, <?php echo $hari; ?> tanggal <?php echo $date; ?> telah dilakukan penerimaan CPO di Tangki PT. INL melalui jalur piping <b>( PKS Sei Mangkei / Dryport *coret salah satu)</b> dengan rincian sebagai berikut :
        </div>
        <div style="margin-left: 30px;">
            <table>
                <tr>
    <td class="no-wrap" style="border-color: white;text-align: left !important;">No. Kontrak - Qty Kontrak </td>
                    <td class="no-wrap" style="border-color: white;text-align: left !important;">:</td>
<td class="no-wrap" style="border-color: white;text-align: left !important;">
    
    <?php
    
        if ($items){
            foreach($items as $res){
                echo '<b>'.$res->origin_no.' &nbsp; - &nbsp; '.num_format($res->transfer_amount).' kg </b> <br/>';
            }
        }
            
    ?>
    
<!--
    101/248924.BOKA - 1200 kg <br/>
    141/248924.BOKA - 1600 kg <br/>
-->
<!--                    ( Terlampir, PIC Logistik)-->
</td>
                </tr>
                <tr>
                    <td class="no-wrap" style="border-color: white;text-align: left !important;">
<!--                        Quantity Kontrak-->
                    </td>
                    <td class="no-wrap" style="border-color: white;text-align: left !important;"></td>
    <td class="no-wrap" style="border-color: white;text-align: left !important;">( Terlampir, PIC Logistik)</td>
                </tr>
                <tr>
                    <td class="no-wrap" style="border-color: white;text-align: left !important;">Boka</td>
                    <td class="no-wrap" style="border-color: white;text-align: left !important;">:</td>
        <td class="no-wrap" style="border-color: white;text-align: left !important; font-weight:bold;"> <?php echo $docno; ?> </td>
                </tr>
            </table>
        </div>
        <div>
            <table class="table">
                <tr>
                    <th rowspan="3">Quality From QC PT. INL (QC Pass) : </th>
                    <th rowspan="2">FFA :</th>
                    <th rowspan="2">M&amp;I</th>
                    <th rowspan="2">PIC QC</th>
                    <th>STATUS</th>
                </tr>
                <tr>
                    <td>Ok / Not Ok / Reject</td>
                </tr>
                <tr>
                    <td><h5> <?php echo $ffa; ?> </h5></td>
                    <td><h5> <?php echo $moist.' &amp; '.$impurities; ?> </h5> </td>
                    <td><h5> <?php echo $pic_qc; ?> </h5> </td>
                    <td> <h5> <?php echo $qcstatus; ?> </h5> </td>
                </tr>
            </table>
        </div>
        <br>
        <div>
            <table class="table">
                <tr>
                    <th rowspan="2">AKTIVITAS</th>
                    <th colspan="3">FFA :</th>
                </tr>
                <tr>
                    
                    <th>IBL</th>
                    <th>OBL</th>
                    <th>KINRA</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="4">Penyegelan bersama Tangki : </th>
                </tr>
                <tr>
                    <td style="text-align: left;">- Tangki Tujuan</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
        <br>
        <div>
            <table class="table">
                <tr>
                    <th rowspan="2">AKTIVITAS</th>
                    <th colspan="3">PENGUKURAN</th>
                    <th colspan="3">PIC</th>
                </tr>
                <tr>
                    <th>SOUNDING (Cm)</th>
                    <th>TEMP (&deg;C)</th>
                    <th>TONASE (Kg)</th>
                    <th>IBL</th>
                    <th>OBL</th>
                    <th>KINRA</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="8">Sounding Bersama (Before)</th>
                </tr>
                <tr>
                    <td style="text-align: left;">a. - Tangki Sumber : <?php echo $source; ?> </td>
                    <td> <?php echo $source_before_cm; ?> </td>
                    <td> <?php echo $source_before_temp; ?>  </td>
                    <td> <?php echo $source_before_tonase; ?>  </td>
                    <td rowspan="2"></td>
                    <td rowspan="2"></td>
                    <td rowspan="2"></td>
                </tr>
                <tr>
                    <td style="text-align: left;">b. - Tangki Tujuan &nbsp; : <?php echo $dest; ?> </td>
                    <td> <?php echo $dest_before_cm; ?>  </td>
                    <td> <?php echo $dest_before_temp; ?> </td>
                    <td> <?php echo $dest_before_tonase; ?> </td>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="8">Sounding Bersama (After)</th>
                </tr>
                <tr>
                    <td style="text-align: left;">c. - Tangki Sumber : <?php echo $source; ?> </td>
                    <td> <?php echo $source_after_cm; ?> </td>
                    <td> <?php echo $source_after_temp; ?>  </td>
                    <td> <?php echo $source_after_tonase; ?> </td>
                    <td rowspan="2"></td>
                    <td rowspan="2"></td>
                    <td rowspan="2"></td>
                </tr>
                <tr>
                    <td style="text-align: left;">d. - Tangki Tujuan &nbsp; : <?php echo $dest; ?> </td>
                    <td> <?php echo $dest_after_cm; ?> </td>
                    <td> <?php echo $dest_after_temp; ?> </td>
                    <td> <?php echo $dest_after_tonase; ?> </td>
                </tr>
            </table>
        </div>
        <div>
            <h5><b>WAKTU KEGIATAN TRANSFER</b></h5>
            <table style="margin-top: -20px;" class="table">
                <tr>
                    <th colspan="2">START</th>
                    <th colspan="2">STOP</th>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <th>Pukul</th>
                    <th>Tanggal</th>
                    <th>Pukul</th>
                </tr>
                <tr>
                    <td> <?php echo $start_date; ?> </td>
                    <td> <?php echo $start_time; ?> </td>
                    <td> <?php echo $end_date; ?> </td>
                    <td> <?php echo $end_time; ?> </td>
                </tr>
            </table>
        </div>
        <div style="margin-left: 20px;">
        <table>
            <tr>
                <th style="border-color: white;text-align: left;">e. Quantity Pengiriman (Kg) keterangan (Poin c - Poin a )</th>
                <th style="border-color: white;text-align: left;">:</th>
                <th style="border-color: white;text-align: left;"> <?php echo $qty_kirim; ?> </th>
            </tr>
            <tr>
                <th style="border-color: white;text-align: left;">f. Quantity Penerimaan (Kg) keterangan (Poin d - Poin b )</th>
                <th style="border-color: white;text-align: left;">:</th>
                <th style="border-color: white;text-align: left;"> <?php echo $qty_terima; ?> </th>
            </tr>
            <tr>
                <th style="border-color: white;text-align: left;">g. Selisih Angka Kirim VS Terima (Kg) keterangan (Poin f - Poin e )</th>
                <th style="border-color: white;text-align: left;">:</th>
                <th style="border-color: white;text-align: left;"> <?php echo $selisih; ?> </th>
            </tr>
            <tr>
                <th style="border-color: white;text-align: left;">h. Persentase keterangan (Poin g / Poin f) x 100 </th>
                <th style="border-color: white;text-align: left;">:</th>
                <th style="border-color: white;text-align: left;"> <?php echo $persentase; ?> % </th>
            </tr>
        </table>
        </div>
        <div>
<!--            <b>Persentase keterangan (Poin g / Poin f) x 100</b>-->
        </div>
        <div>
            <p><b>NOTE : <?php echo $desc; ?> </b></p>
        </div>
        <div>
            Demikian berita acara ini dibuat dengan sebenarnya. <br>
            Sei Mangkei, <?php echo $date; ?>
        </div>
        <div>
            <table class="table">
                <tr>
                    <td style="border-color: white;">Dibuat oleh</td>
                    <td style="border-color: white;">Diperiksa oleh</td>
                    <td style="border-color: white;">Disaksikan oleh</td>
                    <td style="border-color: white;">Disetujui oleh</td>
                </tr>
                <tr style="height: 70px;">
                    <td style="border-color: white;"></td>
                    <td style="border-color: white;"></td>
                    <td style="border-color: white;"></td>
                    <td style="border-color: white;"></td>
                </tr>
                <tr>
                    <td style="border-color: white;">SPV IBL <br/> ( <?php echo $pic_ibl; ?> ) </td>
                    <td style="border-color: white;">SPV OBL <br/> ( <?php echo $pic_obl; ?> )  </td>
                    <td style="border-color: white;">KINRA <br> ( <?php echo $pic_kinra; ?> ) </td>
                    <td style="border-color: white;">Eben Ginting <br> GM OPERATION PLANT 1</td>
                </tr>
            </table>
        </div>
        <div>
            Tembusan :
            <table style="margin-left: 40px;">
                <tr>
                    <td style="border-color: white;text-align: left;width: 100px;"><i>1. PPIC</i></td>
                    <td style="border-color: white;text-align: left;width: 100px;"><i>4. Logistik</i></td>
                    <td style="border-color: white;text-align: left;width: 100px;"><i>6. Produksi</i></td>
                </tr>
                <tr>
                    <td style="border-color: white;text-align: left;width: 100px;"><i>2. MR/QA</i></td>
                    <td style="border-color: white;text-align: left;width: 100px;"><i>5. BC</i></td>
                    <td style="border-color: white;text-align: left;width: 100px;"></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>