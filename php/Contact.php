<?php


require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use phpOffice\PhpSpreadsheet\Writer\Xlsx;

class Contact
{
    private $db;
    private $username;
    function __construct($db)
    {
        $this->db = $db;
        $this->username = $_SESSION['login']['username'];
    }

    public function getAllNumbersByUser()
    {
        $username = $this->username;
        $query = mysqli_query($this->db, "SELECT * FROM nomor WHERE make_by = '$username' ORDER BY id DESC");
        $results = [];
        if (mysqli_num_rows($query) > 0) {
            while ($result = mysqli_fetch_array($query)) {
                $results[] = $result;
            }
        }

        return $results;
    }


    public function addNumber($post)
    {
        $name = filter_var(mysqli_real_escape_string($this->db, $post['name']), FILTER_SANITIZE_STRING);
        $number = filter_var(mysqli_real_escape_string($this->db, $post['number']), FILTER_SANITIZE_STRING);
        $message = filter_var(mysqli_real_escape_string($this->db, $post['message']), FILTER_SANITIZE_STRING);
        $check = mysqli_query($this->db, "SELECT * FROM nomor WHERE nomor = '$number'");
        if (mysqli_num_rows($check) > 0) {
            setFlashMsg('error', 'Number Already Exists!!');
            back('numbers.php');
        }
        if (mysqli_query($this->db, "INSERT INTO nomor VALUES (null,'$name','$number','$message','$this->username')")) {
            setFlashMsg('success', 'Number Added Successfully');
            back('numbers.php');
        }
    }

    public function deleteNumber($id)
    {
        if (mysqli_query($this->db, "DELETE FROM nomor WHERE id = '$id'")) {
            setFlashMsg('success', 'Number Delete Successfully');
            back('numbers.php');
        }
    }


    public function export()
    {

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getProperties()->setCreator('Ilman sunannudin')
            ->setLastModifiedBy('Ilman sunannudin')
            ->setTitle('Data nomor, exported from WA Mpedia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Data nomor exported from m-pedia')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('DATA NOMOR');

        // Add some data

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Nama')
            ->setCellValue('C1', 'Nomor')
            ->setCellValue('D1', 'Pesan');

        // Miscellaneous glyphs, UTF-8

        $no = 1;
        $row = 2;
        $query = mysqli_query($this->db, "SELECT * FROM nomor WHERE make_by = '$this->username'");
        while ($data = mysqli_fetch_array($query)) {
            # code...
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $row, $no)
                ->setCellValue('B' . $row, $data['nama'])
                ->setCellValue('C' . $row, $data['nomor'])
                ->setCellValue('D' . $row, $data['pesan']);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom No
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Set text left untuk kolom NIS
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Set text left untuk kolom NIS
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Set text left untuk kolom NIS


            $no++;
            $row++;
        }
        $sheet->getColumnDimension('A')->setWidth(5); // Set width kolom A
        $sheet->getColumnDimension('B')->setWidth(25); // Set width kolom B
        $sheet->getColumnDimension('C')->setWidth(25); // Set width kolom C
        $sheet->getColumnDimension('D')->setWidth(35); // Set width kolom D

        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Data nomor');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="datanomor.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }


    public function import($file)
    {
        $target_dir = "documents/";
        $target_file = $target_dir . basename($file['name']);
        $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allow = ['xlsx'];
        if (!in_array($ext, $allow)) {
            setFlashMsg('danger', 'Format must be XLSX');
            back('numbers.php');
        }
        $namefile = round(microtime(true)) . mt_rand() . '.' . $ext;
        $target = $target_dir . $namefile;
        if (move_uploaded_file($file["tmp_name"], $target)) {

            $inputFileType = 'Xlsx';
            $inputFileName = $target;


            $reader = IOFactory::createReader($inputFileType);

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($inputFileName);

            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            foreach ($sheetData as $data) {
                $nama = $data['A'];
                $nomor = $data['B'];
                $pesan = $data['C'];
                $check = mysqli_query($this->db, "SELECT * FROM nomor WHERE nomor = '$nomor'");
                if (mysqli_num_rows($check) === 0) {

                    $insert = mysqli_query($this->db, "INSERT INTO nomor VALUES (null,'$nama','$nomor','$pesan','$this->username') ");
                }
            }
            setFlashMsg('success', 'Imorted Successfully');
            back('numbers.php');
        }
        setFlashMsg('danger', 'Server Error !! Try Again Later');
        back('numbers.php');
    }


    public function getContacts($nomor)
    {
        $data = json_decode(file_get_contents('../main/contacts/' . $nomor . '.json'), true);

        foreach ($data as $d) {
            if (array_key_exists('name', $d)) {
                $name = $d['name'];
                if (strpos($d['jid'], '@g.us') == false) {
                    $number = preg_replace("/\D/", "", $d['jid']);
                } else {
                    $number = $d['jid'];
                }
                $check = mysqli_query($this->db, "SELECT * FROM nomor WHERE nomor = '$number'");
                if (mysqli_num_rows($check) === 0) {

                    mysqli_query($this->db, "INSERT INTO nomor VALUES (null,'$name','$number','','$this->username') ");
                }
            }
        }

        setFlashMsg('success', 'Contact Reterived Successfully');
        back('numbers.php');
    }

    public function deleteAll()
    {

        if (mysqli_query($this->db, "DELETE FROM nomor WHERE make_by = '$this->username'") == true) {
            setFlashMsg('success', 'Deleted Successfully');
            back('numbers.php');
        }
    }
}
