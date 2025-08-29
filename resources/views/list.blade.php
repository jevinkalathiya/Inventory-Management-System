@extends('layouts.masterlayout')

@section('title') Category @endsection

@section('content')

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Users List Table -->
        <div class="card">
            
            <div class="card-datatable">
                <table id="category-table" class="datatables-category table border-top" id="datatables-category">
                    <thead>
                        <tr>
                            <th></th>  <!-- control -->
                            <th></th>  <!-- checkbox -->
                            <th>Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!-- Offcanvas to add new category -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNewCategory"
                aria-labelledby="offcanvasNewCategoryLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasNewCategoryLabel" class="offcanvas-title">Create New Category</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
                    <div id="message"></div>
                    <form class="add-new-category pt-0" id="addCategoryForm" method="POST">
                        @csrf
                        <div class="mb-6 form-control-validation">
                            <label class="form-label" for="add-category-name">Category Name</label>
                            <input type="text" class="form-control" id="category-name" placeholder="Electronics"
                                name="category-name" aria-label="Category Name" />
                        </div>
                        <button type="submit" class="btn btn-primary me-3 data-submit">Submit</button>
                        <button type="reset" class="btn btn-label-danger"
                            data-bs-dismiss="offcanvas">Cancel</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Category edit model --}}
        <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm" method="POST">
                    <input type="hidden" id="edit-id">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit-name">
                    </div>
                    <div class="mb-3">
                        <label for="edit-status" class="form-label">Status</label>
                        <select class="form-select" id="edit-status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection