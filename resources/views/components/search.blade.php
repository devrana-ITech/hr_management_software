<div class="row align-items-center mb-3">
  <!-- Entries Dropdown -->
  <div class="col-md-6 col-sm-12">
    <form method="GET" id="entriesForm" class="d-flex align-items-center">
      <label class="d-flex align-items-center mb-0">
        <span class="me-2 fw-semibold text-secondary ml-3 mt-3 mr-2">Show</span>
        <select name="per_page" class="form-select form-select-sm w-auto mt-3"
          onchange="document.getElementById('entriesForm').submit()">
          <option value="2" {{ $perPage == 2 ? 'selected' : '' }}>2</option>
          <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
          <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
          <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
          <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
        </select>
        <span class="ms-2 fw-semibold text-secondary mt-3 ml-2">entries</span>
      </label>
    </form>
  </div>

  <!-- Search Box -->
  <div class="col-md-6 col-sm-12 text-md-end mt-3 mt-md-0">
    <form method="GET" action="{{ $action }}" class="d-flex align-items-center justify-content-md-end">
      <label class="d-flex align-items-center mb-0">
        <span class="me-2 fw-semibold text-secondary mr-2 mt-3">Search:</span>
        <input type="search" name="search" value="{{ $value }}"
          class="form-control form-control-sm w-auto mt-3 mr-2"
          placeholder="{{ $placeholder }}" onkeyup="liveSearch(this.value)">
      </label>
    </form>
  </div>
</div>

<script>
function liveSearch(query) {
    const url = new URL("{{ $action }}", window.location.origin);
    url.searchParams.set('search', query);
    url.searchParams.set('per_page', "{{ $perPage ?? 5 }}");

    fetch(url)
        .then(response => response.json())
        .then(data => {
            document.getElementById('tableBody').innerHTML = data.html;
        });
}
</script>



{{--
<div class="row align-items-center mb-3">
  <div class="col-md-6 col-sm-12">
    <div class="dataTables_length" id="DataTables_Table_0_length">
      <label class="d-flex align-items-center">
        <span class="me-2 ml-3 mt-3 mr-2">Show</span>
        <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0"
          class="form-select form-select-lg w-auto mt-3">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <span class="ms-2 mt-3 mr-2 ml-2">entries</span>
      </label>
    </div>
  </div> --}}

  {{-- <div class="col-md-6 col-sm-12 text-md-end mt-2 mt-md-0">
    <label class="d-flex align-items-center justify-content-md-end">
      <span class="me-2 mt-3 mr-1">Search:</span>
      <input type="search" class="form-control form-control-sm w-auto mt-3 mr-3"
        placeholder="Type to search..." aria-controls="DataTables_Table_0">
    </label>
  </div>
</div> --}}
