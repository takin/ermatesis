<?php
/**
* 
*/
class Obie extends Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->template->set('template');

		$this->loadModel('obie_model','obie');
		$this->data['title'] = "Ekstraksi Informasi Halaman Web <br/> Menggunakan Metode Bootstrapping pada Ontology Based Information Extraction (OBIE)";
	}

	private function render()
	{
		if( $this->is_ajax_request() ) {
			$this->data['ajax']['code'] = 200;
			$this->data['ajax']['view'] = $this->data['body'];
			echo json_encode($this->data['ajax']);
		}
		else {
			$this->template->render($this->data);
		}
	}

	public function index()
	{
		$this->data['body'] = $this->template->view('parts/form_iterasi');
		$this->render();
	}

	public function process_result_occurence()
	{
		if( isset($_GET['iterasi']) ) {

			$iterasi = isset( $_GET['iterasi'] ) ? $_GET['iterasi'] : 0;

        	// lakukan join tabel patternset dengan tabel seedlexicon untuk menghasilkan raw data result_occurence
			$patternset = $this->obie->get_patternset();

        	// apakah ada data patternset ?
			if( is_array( $patternset['rows'] ) ) {
				$patternset = $patternset['rows'];

        		// ambil patternset yang hanya memiliki nilai left dan right 
				foreach ($patternset as $i => $pattern) {
					if( (is_null($pattern['left']) || $pattern['left'] == '') || ( is_null($pattern['right']) || $pattern['right'] == '' ) ) {
						array_splice($patternset, $i, 1);
					}
				}

				// prepare data result_occurence
				foreach ($patternset as $i => $p) {
					// buang index ID karena tidak dibutuhkan pada tabel result_occurence
					unset($patternset[$i]['ID']);

        			// tambahkan index `no`
					$no = $i;
					++$no;
					$patternset[$i]['no'] = "$no";
        			// tambahkan index `iterasi`
					$patternset[$i]['iterasi'] = $iterasi;
				}
				// print_r($patternset);
				// insert data ke dalam database
				$insert_ok = $this->obie->insert_result_occurence( $patternset );
				if( $insert_ok ) {
				// ambil ulang data result_occurence dari database 
					$ro = $this->obie->get_result_occurence_per_iteration( $iterasi );
					$data['result_occurence'] = $ro['rows'];
					$this->data['body'] = $this->template->view('parts/tabel_patternset',$data);
				}
				// jika proses insert resultoccurence gagal
				else {
					$this->data['ajax']['code'] = 400;
					$this->data['body'] = 'Data result occurence tidak dapat dimasukkan!';
				}
			}
        	// jika tidak ada patternset yang dihasilkan
			else {
				$this->data['ajax']['code'] = 400;
				$this->data['body'] = 'Tidak ada result occurence!';
			}
			$this->render();
		}
	}

	public function process_result_pattern()
	{
		if( isset($_GET['iterasi']) ) {
			$iterasi = isset( $_GET['iterasi'] ) ? $_GET['iterasi'] : 0;

			$rp = $this->obie->get_result_occurence( $iterasi );
			$data_result_pattern = $rp['rows'];
			
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

			// insert data ke dalam tabel
			$insert_ok = $this->obie->insert_result_pattern( $data_result_pattern );
			// tampilkan data hasil insert jika proses insert berhasil
			if( $insert_ok ) {
				$this->data['body'] = $this->template->view('parts/tabel_result_pattern',array('result_pattern'=>$data_result_pattern));
			}
			// jika proses insert gagal
			else {
				$this->data['ajax']['code'] = 400;
				$this->data['body'] = 'Result pattern tidak dapat dimasukkan ke dalam database';
			}
			$this->render();
		}
	}

	public function create_extraction()
	{
		
	}

	public function process_score_extraction()
	{
		if( isset($_GET['iterasi']) ) {
			$iterasi = $_GET['iterasi'];

			// ambil data result_patter berdasarkan iterasi
			$rp = $this->obie->get_result_pattern( $iterasi );
			$result_pattern = $rp['rows'];
			$score_extraction = $this->obie->get_patternset_for_set_of_extraction( $result_pattern );

			$ro = $this->obie->get_result_occurence(1);
			$ro = $ro['rows'];

			/* -------------------------------------------------------------------------------------------------------------
			| kolom dalam tabel skorekstraksi adalah ['match','no','RlogF','iterasi']
			| `no` yang digunakan adalah no dari resultpattern, sehingga kita perlu melakukan preparing 
			| data terlebih dahulu yaitu dengan menggabungkan array $raw_score_extraction dengan array $data_result_pattern
			| --------------------------------------------------------------------------------------------------------------
			| dari array $data_result_pattern yang dibutuhkan adalah index `no` dan `RlogF`
			| sedangkan dari array $score_extraction yang dibutuhkan adalah index `match`
            */
            foreach ($score_extraction as $i => $score) {
                // push index match ke dalam array data_result_pattern
                // $data_score_extraction[$i]['match'] = $score['match'];
                
                foreach ($result_pattern as $n => $p) {
                	if( $score_extraction[$i]['left'] == $p['left'] && $score_extraction[$i]['right'] == $p['right'] ) {
                		// $score_extraction[$i]['no'] = $p['no'];
                		// $score_extraction[$i]['RlogF'] = $p['RlogF'];
                		// $score_extraction[$i]['iterasi'] = $p['iterasi'];
                	}
                }
                // unset($score_extraction[$i]['Fi']);
                // unset($score_extraction[$i]['Ni']);
                // unset($score_extraction[$i]['left']);
                // unset($score_extraction[$i]['right']);
            }
            print '<pre/>';
            print_r($score_extraction);
            // print_r($result_pattern);
            // print_r($ro);
		}
	}
}