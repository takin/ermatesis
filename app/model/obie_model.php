<?php

class Obie_Model extends Database 
{
	
	function __construct(){
		parent::__construct();
	}

    /** 
     * -----------------------------------------------------------------------------------------------------
     * method untuk mengambil data patternset dimana kolom match ( dari tabel patternset ) dengan kolom seed
     * ( dari tabel seedlexicon ) harus match
     * -----------------------------------------------------------------------------------------------------
     * @return [array] patternset
     * -----------------------------------------------------------------------------------------------------
     */
    public function get_patternset()
    {
    	// join tabel seedlexicon dengan tabel patternset dengan kriteria kolom seed dengan kolom atch harus sama
    	$sql = "SELECT p.* FROM seedlexicon s INNER JOIN patternset p ON  s.seed = p.match ORDER BY s.ID";

    	// eksekusi query dan kembalikan ke pemanggil
    	return $this->query($sql);
    }

    /**
     * method untuk menghitung nilai Fi. Fi adalah jumlah dari kemunculan tiap pattern
     * dalam resultoccurence
     * @return [type] [description]
     */
    
    public function compute_set_of_pattern( $iterasi )
    {
    	// ambil result occurence dahulu
    	$result_occurence = $this->get_result_occurence( $iterasi );
    	
    	$return_data = array();

    	if( $result_occurence['num_rows'] > 0 ) {
    		// iterasi untuk melakukan perhitungan untuk masing-masing result dari result_occurence
    		foreach ($result_occurence['rows'] as $i => $occurence) {

    			$fi_sql = "SELECT * FROM resultoccurence WHERE `left`='$occurence[left]' AND `right`='$occurence[right]' GROUP BY `left`,`match`,`right`";
    			$fi = $this->query($fi_sql);
    			$fi = ( $fi['num_rows'] > 0 ) ? $fi['num_rows'] : 0;

    			$ni = 0;
    			$RLogf = 0;
    			if( $fi > 1 ) {
    				$ni_sql = "SELECT * FROM patternset WHERE `left`='$occurence[left]' AND `right`='$occurence[right]' GROUP BY `left`,`match`,`right`";
    				$ni = $this->query($ni_sql);
    				$ni = ( $ni['num_rows'] > 0 ) ? $ni['num_rows'] : 0;
    				if( $ni > 0 ) {
    					$RLogf = ($fi/$ni)*(log($fi,2));
    				}
    			}
    			
    			$return_data['fi'][$i] = $fi;
    			$return_data['ni'][$i] = $ni;
    			$return_data['rlogf'][$i] = $RLogf;
    		}
    		return $return_data;
    	}
    	
    	return FALSE;
    }

    public function get_result_occurence_per_iteration( $iterasi )
    {
        $sql = "SELECT `left`,`match`,`right` FROM resultoccurence WHERE iterasi=$iterasi";
        return $this->query($sql);
    }

    public function get_result_occurence( $iterasi )
    {
    	$sql = "SELECT `left`,`match`,`right` FROM resultoccurence WHERE iterasi=$iterasi GROUP BY `left`,`right`";
    	return $this->query($sql);
    }

    /**
     * -------------------------------------------------------------------------------------------------------
     * method untuk menginputkan data hasil get_pattern_and_seedlexicon yang sudah dibuang kolom id nya
     * dan ditambahkan kolom iterasi, sesuai dengan nilai iterasi dari permintaan dari client.
     * data yang di-insert hanya data yang memiliki nilai 'left' dan 'right' 
     * ( nilai kedua kolom tersebut tidak boleh null ) filter terhadap kolom ini sudah dilakukan di controller
     * -------------------------------------------------------------------------------------------------------
     * @param  array $data ['no',left','match','right','iterasi']. *$data['no'] -> dimasukkan dari hasil iterasi di 
     *               dalam method dan tidak dikirimkan dari controller
     * @return void boolean
     * -------------------------------------------------------------------------------------------------------
     */
    public function insert_result_occurence( $data )
    {
    	foreach ( $data as $occurence ) {
    		// build the query
    		$sql = "INSERT INTO resultoccurence (`no`,`left`,`match`,`right`,`iterasi`) VALUES('{$occurence['no']}','{$occurence['left']}','{$occurence['match']}','{$occurence['right']}','{$occurence['iterasi']}')";

    		// run the query parameter FALSE berfungsi untuk mencegah method query untuk melakukan 
    		// auto fetching data ( karena ini adalah proses insert data )
    		$ok = $this->query($sql, FALSE);
    		// jika masing-masing query berhasil, maka set $resturn menjadi TRUE
    		if( ! $ok ) {
    			// jika query gagal, maka hentikan proses iterasi insert data dan set $resturn menjadi FALSE
    			$return = FALSE;
    			break;
    		}
    	}
    	
    	return TRUE;
    }

    public function insert_result_pattern( $data )
    {
    	$return = TRUE;
    	foreach ($data as $i => $res) {
            $sql = "INSERT INTO resultpattern (`no`,`left`,`right`,`Fi`,`Ni`,`RlogF`,`iterasi`) VALUES('{$res['no']}','$res[left]','$res[right]','$res[Fi]','$res[Ni]','$res[RlogF]','$res[iterasi]')";
            $ok = $this->query($sql, FALSE);
            if( ! $ok ){
                $return = FALSE;
                break;
            }
        }
        return $return;
    }

    public function get_result_pattern( $iterasi )
    {
        $sql = "SELECT * FROM resultpattern WHERE iterasi = $iterasi";
        return $this->query($sql);
    }

    public function get_patternset_for_set_of_extraction( $dataset )
    {
        $set_of_extraction = array();
        $dataset = $this->get_result_pattern(1)['rows'];
        foreach ($dataset as $i => $data) {
            $sql = "SELECT LCASE(`match`) AS `match`,`left`,`right` FROM patternset WHERE `left`='$data[left]' AND `right`='$data[right]' GROUP BY `match`";
            $set_of_extraction[] = $this->query($sql)['rows'];
        }
        return $set_of_extraction;
    }

    public function insert_score_extraction( $score_extraction_data )
    {
        foreach ($score_extraction_data as $i => $data) {
            $sql = "INSERT INTO skorekstraksi(`match`,`no`,`RlogF`,`iterasi`) VALUES('$data[match]','$data[no]','$data[RlogF]','$data[iter]')";
            $ok = $this->query($sql, FALSE);
            if( ! $ok ) {
                return FALSE;
                break;
            }
        }
        return TRUE;
    }

    public function compute_best_extraction()
    {
        # code...
    }

    public function insert_best_extraction( $best_extraction_data )
    {
        foreach ($best_extraction_data as $i => $data) {
            $sql = "INSERT INTO bestextraction(`match`,`score`,`iterasi`) VALUES('$data[match]','$data[jum]','$data[iter]')";
            $ok = $this->query( $sql, FALSE );
            if( ! $ok ) {
                return FALSE;
                break;
            }
        }
        return TRUE;
    }

}
