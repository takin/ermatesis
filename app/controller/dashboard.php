<?php

class Dashboard extends Controller {

    function __construct() {

        /*
         * karena kita melakukan overriding terhadap konstruktor kontroller utama,
         * maka kontruktor method utama juga perlu dipanggil.
         * knstruktor controller utama berisi proses instansiasi model dan template
         */
        parent::__construct();
        $this->template->set('template');
        
        /* 
         * load model yang dibutuhkan untuk menangani data
         * parameter ke dua berfungsi sebagai alias dari model sehingga proses pemanggilan model 
         * cukup dengan memanggil nama aliasnya saja $this->main
         */
        $this->loadModel('obie_model', 'obie');
        $this->data['title'] = "Ekstraksi Informasi Halaman Web <br/> Menggunakan Metode Bootstrapping pada Ontology Based Information Extraction (OBIE)";
    }

    private function response()
    {
        if( $this->is_ajax_request() ) {
            $this->data['ajax']['code'] = 200;
            $this->data['ajax']['data'] = $this->data['body'];
            echo json_encode($this->data['ajax']);
        }
        else {
            $this->template->render($this->data);
        }
    }

    public function index() {
        $this->data['body'] = $this->template->view('parts/form_iterasi');
        $this->response();
    }

    /** 
     * --------------------------------------------------------------------------------
     * method untuk men-generate result occurence. result occurence diciptakan melalui 
     * proses agregasi tabel patternset dengan seedlexicon
     * --------------------------------------------------------------------------------
     * @return array data dari result_occurence
     * --------------------------------------------------------------------------------
     */
    public function generate_result_occurence()
    {
        $iterasi = isset( $_GET['iterasi'] ) ? $_GET['iterasi'] : 0;
        // get the patternset that match with the seed lexicon
        $patternset = $this->obie->get_patternset();

        // generate table to display the result
        // karena yang akan ditampilkan hanya yang memiliki left dan right
        // maka kita perlu membuang result yang left dan atau rightnya yang kosong
        $data['pattern'] = array();

        // data yang akan digunakan oleh 
        $result_occurence_data = array();

        // cek apakah kembalian dari get_pattern ada atau tidak
        // jika ada, maka lakukan proses untuk memfilter data patter yang 
        // kolo left dan right nya tidak null/kosong
        if( $patternset['num_rows'] > 0 ) {

            foreach ( $patternset['rows'] as $i => $p ) {

                // apakah field left dan right tidak kosong ?
                if( ! is_null($p['left']) && ! is_null($p['right']) ) {

                    // isikan baris ke dalam array data pattern
                    // array ini akan digunakan untuk men-generate tabel tampilan di view/parts/table_patternset.php
                    $data['pattern'][] = $p;

                    /** 
                     * -------------------------------------------------------------------------------------------------------
                     * duplikat array pattern ke dalam array result_occurence_data 
                     * array ini dibutuhkan untuk dimasukkan ke dalam tabel resultoccurence
                     * tabel resultoccurence memiliki field ['ID','no','left','match','right','iterasi']
                     * ID pada tabel ini adalah autoinc sehingga tidak perlu dilewatkan
                     * oleh karena itu, kita perlu membuang index ID dari array result_occurence_data
                     * serta menambahkan index 'iterasi' dengan nilai sesuai dengan nilai iterasi yang dilewatkan oleh user
                     * dan 'no' sesuai dengan nomer iterasi $i + 1;
                     * --------------------------------------------------------------------------------------------------------
                     */ 
                    
                    // duplikat isi array pattern ke result_occurence_data
                    $result_occurence_data[$i] = $p;

                    // tambahkan filed iterasi
                    $result_occurence_data[$i]['iterasi'] = $iterasi;

                    // tambahkan field no
                    $no = $i;
                    $result_occurence_data[$i]['no'] = ++$no;

                    // buang index ID
                    unset($result_occurence_data[$i]['ID']);
                }
            }

            $this->data['body'] = $this->template->view('parts/tabel_patternset',$data);
            $this->data['ajax']['data'] = $result_occurence_data;
            $this->data['ajax']['iterasi'] = $iterasi;
        }
        else {
            $this->data['body'] = '<div class="alert alert-danger"><b>Tidak ada Occurence dari Seed yang ada!!</b></div>';
        }
        $this->response();
    }

    /**
     * ---------------------------------------------------------------------------------------------
     * method untuk memproses hasil generate result_occurence
     * method ini untuk menyimpan result occurence ke dalam tabel resultoccurence.
     * data yang sudah dimasukkan kemudian di query kembali untuk menghasilkan data Fi, Ni dan RlogF
     * data ini nantinya akan dimasukkan ke dalam tabel resultpattern
     * ---------------------------------------------------------------------------------------------
     * @return array data result_pattern yang nantinya akan dikirim ke method process_result_pattern
     * @return JSON tabel dalam format JSON untuk ditampilkan di browser
     * ---------------------------------------------------------------------------------------------
     */
    public function process_result_occurence()
    {
        if( isset($_POST['roc']) ) {
            $iterasi = $_POST['iterasi'];
            $result_occurence_data = $_POST['occurence_data'];
            // masukkan data result_occurence ke dalam tabel resultoccurence
            $insert_ok = $this->obie->insert_result_occurence( $result_occurence_data );

            if( $insert_ok ) {
                 /**
                 * jika proses insert data result_occurence berhasil, maka langkah selanjutnya adalah
                 * 1. mengambil data result_occurence dan dihitung jumlah kemunculan dari masing-masing patern
                 * 2. generate resultofpattern dengan tahapan:
                 *    1. hitung Fi, Ni, RLog
                 *    2. tampilkan dalam bentuk tabel, serta simpan data hasil perhitungannya ke dalam tabel result pattern
                 */
                // ambil kembali data result occurent dari dalam tabel resulroccurence
                // data harus diambil dari dalam tabel karena pada proses query ada group by untuk membuang data yang identik
                // sehingga nantinya jumlah data akan sama dengan jumlah data perhitungan Fi, Ni dan RlogF
                $data_result_pattern = $this->obie->get_result_occurence( $iterasi )['rows'];

                // lakukan perhitungan nilai Ri, Fi dan RLogF
                $rlog_fi_and_ni = $this->obie->compute_set_of_pattern( $iterasi );

                // push array rlog_fi_and_ni ke dalam array data_result_pattern
                // hal ini dilakukan agar data ini gampang dimasukkan ke dalam tabel resultpattern
                $no = 0;
                foreach ($data_result_pattern as $i => $oc) {
                    $no++;
                    $data_result_pattern[$i]['no'] = "p{$no}";
                    $data_result_pattern[$i]['Fi'] = $rlog_fi_and_ni['fi'][$i];
                    $data_result_pattern[$i]['Ni'] = $rlog_fi_and_ni['ni'][$i];
                    $data_result_pattern[$i]['RlogF'] = $rlog_fi_and_ni['rlogf'][$i];
                    $data_result_pattern[$i]['iterasi'] = $iterasi;
                }

                // kopi array data_result_occurence ke data_result_occurence_display
                // hal ini dilakukan karena data_result_occurence akan dilakukan proses pembuangan data 
                // yang memiliki RlogF 0, sedangkan array data_result_occurence_display akan digunakan untuk
                // menampilkan hasil ke browser dalam bentuk yang masih utuh ( data yang berisi RlogF = 0 tidak di buang )
                $data['result_occurence'] = $data_result_pattern;

                // buang index array yang berisi data RlogF 0
                foreach ($data_result_pattern as $i => $res) {
                    if( $res['RlogF'] == 0 ) {
                        unset($data_result_pattern[$i]);
                    }
                }

                // kirimkan data yang sudah di filter ke client 
                // untuk dikirmkan lagi untuk proses process_result_pattern
                $this->data['ajax']['data'] = $data_result_pattern;

                // generate the display
                $this->data['body'] = $this->template->view('parts/tabel_result_pattern',$data);
            }
            else {
                $this->data['ajax']['code'] = 400;
                $this->data['body'] = '<div class="alert alert-danger">Data tidak dapat disimpan</div>';
            }
            // generate the response
            $this->response();
        }
    }


    public function process_result_pattern()
    {
        if( isset($_POST['rp']) ) {
            $data_result_pattern = $_POST['result_pattern'];

             // masukkan data_result_pattern ke dalam tabel resultpattern
            $q_ok = $this->obie->insert_result_pattern( $data_result_pattern );

            if( $q_ok ) {

            }
        }
    }

    public function test()
    {
        $iterasi = 1;
        $result_occurence = $this->obie->get_result_occurence( $iterasi );
        $rlog_fi_and_ni = $this->obie->compute_set_of_pattern($iterasi);
        foreach ($result_occurence['rows'] as $i => $oc) {
            $result_occurence['rows'][$i]['fi'] = $rlog_fi_and_ni['fi'][$i];
            $result_occurence['rows'][$i]['ni'] = $rlog_fi_and_ni['ni'][$i];
            $result_occurence['rows'][$i]['rlogf'] = $rlog_fi_and_ni['rlogf'][$i];
        }

        foreach ($result_occurence['rows'] as $i => $res) {
            if( $res['rlogf'] == 0 ) {
                unset($result_occurence['rows'][$i]);
            }
        }

        print '<pre/>';
        print_r($result_occurence);
        print_r($rlog_fi_and_ni);
    }
}