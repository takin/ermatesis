<dic class="row">
	<div class="col-lg-2"></div>
	<div class="col-lg-8">
		<div class="row">
			<table class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th>#</th>
						<th>Left Context</th>
						<th>Seed Element</th>
						<th>Right Context</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					foreach ( $pattern as $n => $p ) {
						++$n;
						echo "<tr><td>$n</td><td>$p[left]</td><td>$p[match]</td><td>$p[right]</td>";
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