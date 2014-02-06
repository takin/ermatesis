<dic class="row">
	<div class="col-lg-2"></div>
	<div class="col-lg-8">
		<div class="row">
			<table class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th>#</th>
						<th>Left</th>
						<th>Right</th>
						<th>Fi</th>
						<th>Ni</th>
						<th>RlogF</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $result_occurence as $roc ) {
						echo "<tr>";
						echo "<td>$roc[no]</td>";
						echo "<td>$roc[left]</td>";
						echo "<td>$roc[right]</td>";
						echo "<td>$roc[Fi]</td>";
						echo "<td>$roc[Ni]</td>";
						echo "<td>$roc[RlogF]</td>";
					}
					?>
				</tbody>
			</table>
		</div>
		<div class="row">
			<div class="pull-right">
				<a href="dashboard/create_extraction" class="btn btn-success process" id="createExtraction"><i class="fa fa-check-square-o"></i> Create Extraction</a>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="col-lg-2"></div>
</div>