<div class="row">
	<div class="col-lg-2"></div>
	<div class="col-lg-8">
		<div class="row">
			<table class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th>#</th>
						<th>Extraction</th>
						<th>No. Pattern</th>
						<th>Score</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					foreach ( $result_extraction as $n => $e ) {
						echo '<tr><td>e'.++$n.'</td>';
						echo "<td>$e[extraction]</td>";
						echo "<td>";
						foreach ($e['np'] as $i => $enp) {
							echo '<div class="label label-success">'.$enp.'</div>&nbsp;';
						}
						echo "</td>";
						echo "<td>$e[score]</td>";
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-lg-2"></div>
</div>