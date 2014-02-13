<?php
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
				
				$insert_ok = ( isset($_GET['insert']) ) ? $this->obie->insert_result_occurence( $patternset ) : 1 ;

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
			$insert_ok = (isset($_GET['insert'])) ? $this->obie->insert_result_pattern( $data_result_pattern ) : 1;
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

	public function compute_score_extraction()
	{
		// get the 
	}

	public function process_score_extraction()
	{
		if( isset($_GET['iterasi']) ) {
			$iterasi = $_GET['iterasi'];

			// ambil data result_patter berdasarkan iterasi
			$rp = $this->obie->get_result_pattern( $iterasi );
			$result_pattern = $rp['rows'];

			// buang data result_pattern yang nilai RlogF nya = 0
			foreach ($result_pattern as $i => $p) {
			 	if( $p['RlogF'] == 0 ) {
			 		unset($result_pattern[$i]);
			 	}
			}

			$result_extraction_data = $this->obie->get_patternset_for_set_of_extraction( $result_pattern );

			$ro = $this->obie->get_result_occurence(1);
			$ro = $ro['rows'];

			/* -------------------------------------------------------------------------------------------------------------
			| kolom dalam tabel skorekstraksi adalah ['match','no','RlogF','iterasi']
			| `no` yang digunakan adalah no dari resultpattern, sehingga kita perlu melakukan preparing 
			| data terlebih dahulu yaitu dengan menggabungkan array $raw_score_extraction dengan array $data_result_pattern
			| --------------------------------------------------------------------------------------------------------------
			| dari array $data_result_pattern yang dibutuhkan adalah index `no` dan `RlogF`
			| sedangkan dari array $result_extraction_data yang dibutuhkan adalah index `match`
            */
            foreach ($result_extraction_data as $i => $score) {
                foreach ($result_pattern as $n => $p) {
                	if( $result_extraction_data[$i]['left'] == $p['left'] && $result_extraction_data[$i]['right'] == $p['right'] ) {
                		$result_extraction_data[$i]['nopattern'] = $p['no'];
                		$result_extraction_data[$i]['RlogF'] = $p['RlogF'];
                		$result_extraction_data[$i]['iterasi'] = $p['iterasi'];
                	}
                }
                // buang index left dan right karena tidak dibutuhkan di dalam tabel resultextraction
                unset($result_extraction_data[$i]['left']);
                unset($result_extraction_data[$i]['right']);
            }

            $insert_ok = ( isset($_GET['insert']) ) ? $this->obie->insert_result_extraction( $result_extraction_data ) : 1;
            
            if( $insert_ok ) {
            	
            	/**
            	 * jika proses insert data result extraction ke dalam tabel resultextraction berhasil,
            	 * maka proses selanjutnya adalah melakukan perhitungan skor dari masing-masing set extraction
            	 * perhitungan skor didasarkan pada jumlahan dari RlogF dari setiap `match` untuk seriap kemunculannya 
            	 * pada masing-masing `p`, namun jika kemunculannya hanya sekali ( hanya pada salah satu `p` saja ), 
            	 * maka skornya diabaikan ( dianggap 0 )
            	 */
            	
            	// ambil data no pattern
            	foreach ($result_extraction_data as $i => $d) {
            		// array reference digunakan untuk menyimpan semua `match` dengan nilai rlognya.
            		// nantinya array ini akan di "distinct", agar kemunculan 'match' hanya sekali
            		$r[$i] = array('np'=>$d['nopattern'],'ex'=>$d['match'],'rlog'=>$d['RlogF']);

            		// array master adalah temporary array yang berisi semua 'match' yang akan dihitung skor nya
            		$master[] = $d['match'];

            		// array untuk menampilkan p pada header tabel
            		$nopattern[] = $d['nopattern'];
            	}
            	
            	// buang index array yang memiliki nilai sama
            	// misalnya index ke 0 dan ke 3 berisi "motor cycle diary", maka salah satunya dibuang
            	$master = array_unique($master);

            	// array reference juga harus dibuang duplikasinya
            	foreach ($r as $ref) {
            		$reference[] = array_unique($ref);
            	}

            	// array final adalah array dari data hasil extraksi yang memuat 'match' atau 'extraction' dan skor nya.
            	foreach ($master as $m) {
            		$final[] = array('extraction'=>$m,'score'=>0,'np'=>array());
            	}

            	// lakukan iterasi pada semua data dari array final untuk menambahkan nilai skor nya
            	foreach ($final as $i => $f) {
            		// array tmp adalah array untuk merekam index dari array reference beserta dengan nilai rlog nya
            		// array ini dibutuhkan untuk menentukan apakah nilai skor harus ditambahkan atau tidak.
            		// jika jumlah key dari array ini lebih dari satu, maka nilai skor dari array final ditambahkan
            		// dengan semua isi dari array ini
            		$tmp = array();

            		// lakukan iterasi pada array reference untuk mencocokkan nilai extraction dariarray reference
            		// dengan nilai extraction dari array master
            		foreach ($reference as $n => $r) {
            			// jika isi array reference sama dengan array final maka ambil nilai rlog dari array referece
            			// dan masukkan ke dalam array tmp
            			if( $f['extraction'] == $r['ex'] ) {
            				$tmp[$n] = $r['rlog'];
            				$final[$i]['np'][] = $r['np'];
            			}
            		}

            		// cek apakah jumlah index dari array tmp lebih dari satu.
            		// jika terdapat lebih dari satu index ( artinya, kemunculan extraction lebih dari satu p )
            		// maka update nilai index score pada array final dengan menjumlahkan semua isi array tmp
            		if( count($tmp) > 1 ) {
            			$final[$i]['score'] = array_sum($tmp);
            		}
            	}

            	$data['result_extraction'] = $final;

            	// ambil hanya masing-masing satu dari p
            	$data['np'] = array_unique($nopattern);
            	// $data['np'] = reset($data['np']);
            	$data['ref'] = $reference;

            	// $insert_ok = ( isset($_GET['insert_se']) ) ? $this->obie->insert_score_extraction() : 1;
            	
            	$this->data['body'] = $this->template->view('parts/tabel_result_extraction',$data);
            }
            $this->render();
		}
	}
}