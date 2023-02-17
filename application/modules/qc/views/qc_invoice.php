<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo $h2title; ?> </title>
    <style>
        body{
            margin: 10px;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        }
        .wrap{
            box-sizing: border-box;
            border: 3px solid #000;
            padding: 0;
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

        .s_tengah {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .tengah {
            width: 50%;
            height: 50%;
        }


    </style>
</head>
<body>
    <div class="wrap">
        <div>
            <table class="table">
                <tr>
                    <td rowspan="4">
                        <img width="100px" src="https://inl.co.id/wp-content/uploads/2021/06/LOGO-INL-768x236.png" alt="" srcset="">
                    </td>
                    <td rowspan="3">
                        <h3><u>PT. INDUSTRI NABATI LESTARI</u> <br><br> PABRIK MINYAK GORENG</h3> <br>
                        Kantor Pusat : Komp.KEK Sei Mangkei, Kav.2-3, Kec. Bosar Maligas, Kab. Simalungun, Sumatera Utara, 21184
                    </td>
                    <th class="no-wrap">No. Dokumen</th>
                    <th class="no-wrap">Tgl. Berlaku</th>
                </tr>
                <tr>
                    <td class="no-wrap">isi</td>
                    <td class="no-wrap">isi</td>
                </tr>
                <tr>
                    <td class="no-wrap">No. Revisi</td>
                    <td class="no-wrap">Halaman</td>
                </tr>
                <tr>
                    <th><h3>QC PASSED</h3></th>
                    <td class="no-wrap">-</td>
                    <td class="no-wrap">1 dari 1</td>
                </tr>
            </table>
        </div>
        <div>
            <table>
                <tr>
                    <th style="text-align: left;border-color: white;">No</th>
                    <th style="text-align: left;border-color: white;">:</th>
                    <th style="text-align: left;border-color: white;"> <?php echo $docno; ?> </th>
                </tr>
                <tr>
                    <th style="text-align: left;border-color: white;">Contract-No</th>
                    <th style="text-align: left;border-color: white;">:</th>
                    <th style="text-align: left;border-color: white;"> <?php echo $contract; ?> </th>
                </tr>
                <tr>
                    <th style="text-align: left;border-color: white;">Tanggal</th>
                    <th style="text-align: left;border-color: white;">:</th>
                    <th style="text-align: left;border-color: white;"> <?php echo $date ?> </th>
                </tr>
                <tr>
                    <th style="text-align: left;border-color: white;">Material</th>
                    <th style="text-align: left;border-color: white;">:</th>
                    <th style="text-align: left;border-color: white;"> <?php echo $product; ?> </th>
                </tr>
                <tr>
                    <th style="text-align: left;border-color: white;">Source Carriage </th>
                    <th style="text-align: left;border-color: white;">:</th>
                    <th style="text-align: left;border-color: white;"> <?php echo $gerbong; ?> </th>
                </tr>
                <tr>
                    <th style="text-align: left;border-color: white;">Customer</th>
                    <th style="text-align: left;border-color: white;">:</th>
                    <th style="text-align: left;border-color: white;"> <?php echo $supplier.' - '.$customer; ?> </th>
                </tr>
                <tr>
                    <th style="text-align: left;border-color: white;">Destination Tank</th>
                    <th style="text-align: left;border-color: white;">:</th>
                    <th style="text-align: left;border-color: white;"> <?php echo $dest; ?> </th>
                </tr>
                <tr>
                    <th style="text-align: left;border-color: white;">Kepada</th>
                    <th style="text-align: left;border-color: white;">:</th>
                    <th style="text-align: left;border-color: white;">Logistik</th>
                </tr>
            </table>
        </div>
        <br>
        <div class="s_tengah">
            <div class="tengah">
                <table class="table">
                    <tr>
                        <th>Parameter Analisa</th>
                        <th>Hasil</th>
                    </tr>
                    <tr>
                        <td style="text-align: left;">1. Free Fatty Acid</td>
                        <td> <?php echo $ffa; ?> </td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">2. Moist </td>
                        <td> <?php echo $moist; ?> </td>
                    </tr>
                     <tr>
                        <td style="text-align: left;">3. Imp </td>
                        <td> <?php echo $imp; ?> </td>
                    </tr>
<!--
                    <tr>
                        <td style="text-align: left;">3. L.C</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">4. IV</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
-->
                </table>
            </div>
        </div>
        <br>
        <div>
            <table class="table">
                <tr>
                    <th style="text-align: left;border-color: white;">Disposisi</th>
                    <td style="text-align: left;border-color: white;">:</td>
                    <td style="text-align: left;border-color: white;"><input type="checkbox" name="" id=""> OK</td>
                    <td style="text-align: left;border-color: white;"><input type="checkbox" name="" id=""> HOLD</td>
                    <td style="text-align: left;border-color: white;"><input type="checkbox" name="" id=""> NOT OK</td>
                </tr>
            </table>
        </div>
        <div>
            <table style="float: right;margin-top: 10px;margin-right: 30px;">
                <tr>
                    <td style="border-color: white;"></td>
                </tr>
                <tr>
                    <td style="border-color: white;">____________</td>
                </tr>
                <tr>
                    <th style="border-color: white;"> <?php echo $user; ?> <br/> Analis </th>
                </tr>
            </table>
        </div>
        <br><br><br><br><br><br>
    </div>
</body>
</html>