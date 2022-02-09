<?php


class Autoresponder
{
    private $db;
    function __construct($db)
    {
        $this->db = $db;
    }

    public function byUser()
    {

        $own = $_SESSION['login']['username'];
        $data = mysqli_query($this->db, "SELECT * FROM autoreply WHERE make_by = '$own' ORDER BY id DESC ");
        $result = [];
        if (mysqli_num_rows($data) > 0) {
            while ($d = mysqli_fetch_array($data)) {
                $result[] = $d;
            }
        }
        return $result;
    }

    public function addAutoRespond(array $post)
    {

        $nomor = filter_var(mysqli_real_escape_string($this->db, $post['nomor']), FILTER_SANITIZE_STRING);
        $keyword = filter_var(mysqli_real_escape_string($this->db, $post['keyword']), FILTER_SANITIZE_STRING);
        $respond = filter_var(mysqli_real_escape_string($this->db, $post['respond']), FILTER_SANITIZE_STRING);
        $username = $_SESSION['login']['username'];
        $cek = mysqli_query($this->db, "SELECT * FROM autoreply WHERE nomor = '$nomor' AND keyword = '$keyword' ");
        if ($_FILES['media']['error']) {

            if (mysqli_num_rows($cek) > 0) {
                setFlashMsg('danger', 'keyword with that number already exists!');
                back('autoresponder.php');
            }

            if (mysqli_query($this->db, "INSERT INTO autoreply(keyword, `response`, `nomor`, make_by) VALUES ('$keyword','$respond','$nomor','$username') ")) {
                setFlashMsg('success', 'Auto Reply Successfully Added');
                back('autoresponder.php');
            };
        } else {
            $file = $_FILES['media'];
            $media = uploadFile($file);
            if ($media != false) {
                $urlmedia = BASE_URL . 'pages/' . $media;
                if (mysqli_query($this->db, "INSERT INTO autoreply(keyword, `response`, media, `nomor`, make_by) VALUES ('$keyword','$respond', '$urlmedia', '$nomor','$username') ")) {
                setFlashMsg('success', 'Auto Reply Successfully Added');
                back('autoresponder.php');
            };
            }
            setFlashMsg('danger', 'Server Error !! Try Again Later');
            back('autoresponder.php');
        }
    }

    public function deleteAutoRespond($id)
    {
        if (mysqli_query($this->db, "DELETE FROM autoreply WHERE id = '$id'")) {
            setFlashMsg('success', 'Deleted Successfully');
            back('autoresponder.php');
        }
        setFlashMsg('danger', 'System Error');
        back('autoresponder.php');
    }
}
