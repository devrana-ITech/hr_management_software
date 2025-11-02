
<div class="row align-items-center mb-3">
  <div class="col-md-6 col-sm-12">
  <form method="GET" id="entriesForm" class="d-flex align-items-center">
    <span class="me-2 mt-3 mr-2">Show</span>
    <select name="per_page" class="form-select form-select-lg w-auto mt-3"
            onchange="document.getElementById('entriesForm').submit()">
      <option value="2" {{ $perPage == 2 ? 'selected' : '' }}>2</option>
      <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
      <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
    </select>
    <span class="ms-2 mt-3 mr-2">entries</span>
  </form>
   </div>
  </div>


{{-- <div class="row align-items-center mb-3">
  <div class="col-md-6 col-sm-12">
    <div class="dataTables_length" id="DataTables_Table_0_length">
    <form method="GET" id="entriesForm" >
      <label class="d-flex align-items-center">
        <span class="me-2 ml-3 mt-3 mr-2">Show</span>
        <select name="DataTables_Table_0_length" id="entriesForm"
          class="form-select form-select-lg w-auto mt-3" onchange="document.getElementById('entriesForm').submit()">
        <option value="2" {{ $perPage == 2 ? 'selected' : '' }}>2</option>
      <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
      <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
        </select>
        <span class="ms-2 mt-3 mr-2 ml-2">entries</span>
      </label>
       </form>
    </div>
  </div> --}}
