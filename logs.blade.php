@extends('log-viewer::adminator._master')

<?php /** @var  Illuminate\Pagination\LengthAwarePaginator  $rows */ ?>

@push('logpage-title')
    @lang('View Logs List')
@endpush

@section('log-content')
    <table class="w-full">
        <thead>
        <tr>
            @foreach($headers as $key => $header)
                <th scope="col" class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                    @if ($key == 'date')
                        <span>{{ $header }}</span>
                    @else
                        <span class="level-{{ $key }} px-2 rounded inline-flex items-center">
                            @include('log-viewer::adminator.icon-maker', ['icon' => $key, 'size' => 5]) {{ $header }}
                        </span>
                    @endif
                </th>
            @endforeach
            <th scope="col" class="text-right">@lang('Actions')</th>
        </tr>
        </thead>
        <tbody>
        @forelse($rows as $date => $row)
            <tr class="{{ $loop->index % 2 === 0 ? 'even' : 'odd' }}">
                @foreach($row as $key => $value)
                    <td class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                        @if ($key == 'date')
                            <span>{{ \Carbon\Carbon::parse($value)->format('d/m/Y') }}</span>
                        @elseif ($value == 0)
                            <span>{{ $value }}</span>
                        @else
                            <a href="{{ route('log-viewer::logs.filter', [$date, $key]) }}">
                                <span class="{{ $key }}">{{ $value }}</span>
                            </a>
                        @endif
                    </td>
                @endforeach
                <td class="text-right">
                    <a href="{{ route('log-viewer::logs.show', [$date]) }}">View</a>
                    <a href="{{ route('log-viewer::logs.download', [$date]) }}">Download</a>
                    <a href="#delete-log-modal" data-log-date="{{ $date }}">Delete</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center">
                    <span>@lang('The list of logs is empty!')</span>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    {{ $rows->render() }}
@endsection

@section('log-modals')
{{--    <div id="delete-log-modal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">--}}
{{--        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">--}}
{{--            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>--}}

{{--            <!-- This element is to trick the browser into centering the modal contents. -->--}}
{{--            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>--}}

{{--            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">--}}
{{--                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">--}}
{{--                    <div class="sm:flex sm:items-start">--}}
{{--                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">--}}
{{--                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">--}}
{{--                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />--}}
{{--                            </svg>--}}
{{--                        </div>--}}
{{--                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">--}}
{{--                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">@lang('Delete log file')</h3>--}}
{{--                            <div class="modal-body mt-2">--}}
{{--                                <p class="text-sm text-gray-500"></p>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">--}}
{{--                    <form id="delete-log-form" action="{{ route('log-viewer::logs.delete') }}" method="POST">--}}
{{--                        <input type="hidden" name="_method" value="DELETE">--}}
{{--                        <input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
{{--                        <input type="hidden" name="date" value="">--}}
{{--                        <button type="submit" data-loading-text="@lang('Loading')&hellip;" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">--}}
{{--                            @lang('Delete')--}}
{{--                        </button>--}}
{{--                        <button type="button" data-dismiss="modal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">--}}
{{--                            @lang('Cancel')--}}
{{--                        </button>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection

@push('before-logscripts')
    <script>
        $(function () {
            var deleteLogModal = $('div#delete-log-modal'),
                deleteLogForm  = $('form#delete-log-form'),
                submitBtn      = deleteLogForm.find('button[type=submit]');

            $("a[href='#delete-log-modal']").on('click', function(event) {
                event.preventDefault();
                var date    = $(this).data('log-date'),
                    message = "{{ __('Are you sure you want to delete this log file: :date ?') }}";

                deleteLogForm.find('input[name=date]').val(date);
                deleteLogModal.find('.modal-body p').html(message.replace(':date', date));

                deleteLogModal.modal('show');
            });

            deleteLogForm.on('submit', function(event) {
                event.preventDefault();
                submitBtn.button('loading');

                $.ajax({
                    url:      $(this).attr('action'),
                    type:     $(this).attr('method'),
                    dataType: 'json',
                    data:     $(this).serialize(),
                    success: function(data) {
                        submitBtn.button('reset');
                        if (data.result === 'success') {
                            deleteLogModal.modal('hide');
                            location.reload();
                        }
                        else {
                            alert('AJAX ERROR ! Check the console !');
                            console.error(data);
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        alert('AJAX ERROR ! Check the console !');
                        console.error(errorThrown);
                        submitBtn.button('reset');
                    }
                });

                return false;
            });

            deleteLogModal.on('hidden.bs.modal', function() {
                deleteLogForm.find('input[name=date]').val('');
                deleteLogModal.find('.modal-body p').html('');
            });
        });
    </script>
@endpush
