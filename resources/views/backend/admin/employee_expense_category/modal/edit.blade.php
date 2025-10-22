<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('employee_expense_categories.update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">
	<div class="row px-2">
		<div class="col-md-12">
		<div class="form-group">
		   <label class="control-label">{{ _lang('Name') }}</label>						
		   <input type="text" class="form-control" name="name" value="{{ $expensecategory->name }}" required>
		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
		   <label class="control-label">{{ _lang('Description') }}</label>						
		   <textarea class="form-control" name="description">{{ $expensecategory->description }}</textarea>
		</div>
	</div>

	
		<div class="col-md-12 mt-2">
			<div class="form-group">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Update') }}</button>
		    </div>
		</div>
	</div>
</form>

