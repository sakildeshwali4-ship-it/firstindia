@extends('admin.layouts.master')

@section('title', 'Wallet')

@section('content')
    <div class="body-content">
        <h1 class="page-title-sm">Wallet Coins</h1>

        <div class="border-bottom row mb-3">
            <div class="col-sm-10">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Label.Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Wallet Coins</li>
                </ol>
            </div>
            <div class="col-sm-2 d-flex align-items-center justify-content-end" style="margin-top:-14px">
                <button type="button" class="btn btn-default mw-120" id="add-plan-row">Add Plan</button>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('wallet.update') }}">
            @csrf
            @method('put')
            <div id="delete-package-inputs"></div>

            <div class="table-responsive">
                <table class="table table-striped text-center table-bordered package-table">
                    <thead>
                        <tr style="background: #F9FAFF;">
                            <th>Name</th>
                            <th>Price (Rs)</th>
                            <th>Coins</th>
                            <th>Order</th>
                            <th>Active</th>
                            <th>{{ __('Label.Action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="wallet-plan-rows">
                        @foreach ($packages as $index => $package)
                            <tr data-existing-id="{{ $package->id }}">
                                <td>
                                    <input type="hidden" name="packages[{{ $index }}][id]" value="{{ $package->id }}">
                                    <input name="packages[{{ $index }}][name]" class="form-control" value="{{ old("packages.{$index}.name", $package->name) }}" required>
                                </td>
                                <td><input name="packages[{{ $index }}][price_rupees]" class="form-control" type="number" min="1" value="{{ old("packages.{$index}.price_rupees", $package->price_rupees) }}" required></td>
                                <td><input name="packages[{{ $index }}][coins]" class="form-control" type="number" min="1" value="{{ old("packages.{$index}.coins", $package->coins) }}" data-package-coins required></td>
                                <td><input name="packages[{{ $index }}][sort_order]" class="form-control" type="number" min="0" value="{{ old("packages.{$index}.sort_order", $package->sort_order) }}"></td>
                                <td>
                                    <div class="form-check d-inline-block">
                                        <input type="hidden" name="packages[{{ $index }}][is_active]" value="0">
                                        <input type="checkbox" name="packages[{{ $index }}][is_active]" value="1" class="form-check-input" id="package_active_{{ $index }}" {{ old("packages.{$index}.is_active", $package->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="package_active_{{ $index }}">Show</label>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger delete-plan-row" data-package-id="{{ $package->id }}">Delete</button>
                                </td>
                            </tr>
                        @endforeach

                        @php($newIndex = $packages->count())
                        <tr>
                            <td><input name="packages[{{ $newIndex }}][name]" class="form-control" value="{{ old("packages.{$newIndex}.name") }}" placeholder="Starter"></td>
                            <td><input name="packages[{{ $newIndex }}][price_rupees]" class="form-control" type="number" min="1" value="{{ old("packages.{$newIndex}.price_rupees") }}" placeholder="100"></td>
                            <td><input name="packages[{{ $newIndex }}][coins]" class="form-control" type="number" min="1" value="{{ old("packages.{$newIndex}.coins") }}" data-package-coins></td>
                            <td><input name="packages[{{ $newIndex }}][sort_order]" class="form-control" type="number" min="0" value="{{ old("packages.{$newIndex}.sort_order", $newIndex + 1) }}"></td>
                            <td>
                                <div class="form-check d-inline-block">
                                    <input type="hidden" name="packages[{{ $newIndex }}][is_active]" value="0">
                                    <input type="checkbox" name="packages[{{ $newIndex }}][is_active]" value="1" class="form-check-input" id="package_active_{{ $newIndex }}" {{ old("packages.{$newIndex}.is_active", true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="package_active_{{ $newIndex }}">Show</label>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger delete-plan-row">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <button class="btn btn-default" type="submit">Save Wallet</button>
            </div>
        </form>
    </div>
@endsection

@section('pagescript')
    <script>
        (function() {
            const rowsContainer = document.getElementById('wallet-plan-rows');
            const addRowButton = document.getElementById('add-plan-row');
            const deleteInputsContainer = document.getElementById('delete-package-inputs');

            if (!rowsContainer || !addRowButton || !deleteInputsContainer) {
                return;
            }

            let nextIndex = rowsContainer.querySelectorAll('tr').length;

            const bindDeleteButtons = () => {
                rowsContainer.querySelectorAll('.delete-plan-row').forEach((button) => {
                    if (button.dataset.bound === '1') {
                        return;
                    }

                    button.dataset.bound = '1';
                    button.addEventListener('click', () => {
                        const row = button.closest('tr');
                        const packageId = button.dataset.packageId;

                        if (packageId) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'delete_package_ids[]';
                            input.value = packageId;
                            deleteInputsContainer.appendChild(input);
                        }

                        row?.remove();
                    });
                });
            };

            addRowButton.addEventListener('click', () => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><input name="packages[${nextIndex}][name]" class="form-control" placeholder="Starter"></td>
                    <td><input name="packages[${nextIndex}][price_rupees]" class="form-control" type="number" min="1" placeholder="100"></td>
                    <td><input name="packages[${nextIndex}][coins]" class="form-control" type="number" min="1" placeholder="80"></td>
                    <td><input name="packages[${nextIndex}][sort_order]" class="form-control" type="number" min="0" value="${nextIndex + 1}"></td>
                    <td>
                        <div class="form-check d-inline-block">
                            <input type="hidden" name="packages[${nextIndex}][is_active]" value="0">
                            <input type="checkbox" name="packages[${nextIndex}][is_active]" value="1" class="form-check-input" id="package_active_${nextIndex}" checked>
                            <label class="form-check-label" for="package_active_${nextIndex}">Show</label>
                        </div>
                    </td>
                    <td><button type="button" class="btn btn-sm btn-danger delete-plan-row">Delete</button></td>
                `;

                rowsContainer.appendChild(row);
                nextIndex += 1;
                bindDeleteButtons();
            });

            bindDeleteButtons();
        })();
    </script>
@endsection
