<div class="locateee-field" id="locateee-field-<?= $field_id; ?>">
	<div
		class="locateee-error"
		data-later="<?= $error_data_later; ?>"
		data-address="<?= $error_data_address; ?>"
	>
		<a class="locateee-close" href="#">&times;</a>

		<strong>Error:</strong> <span class="locateee-error-message"></span>
	</div>

	<table border="0" cellpadding="0" cellspacing="0" class="locateee">
		<thead class="locateee">
			<tr class="locateee locateee-first locateee-last">
				<? foreach($columns as $column): ?>
					<th class="locateee" width="<?= $column['width']; ?>">
						<? $required = (isset($column['is_required'])) ? null : null; ?>
						<?= $column['heading'] . $required; ?>
					</th>
				<? endforeach; ?>
			</tr>
		</thead>
		<tbody class="locateee">
			<tr class="locateee locateee-first locateee-last">
				<? foreach($columns as $column): ?>			
					<td class="locateee">
						<?= $column['field']; ?>
					</td>
				<? endforeach; ?>
			</tr>
		</tbody>
	</table>
</div>