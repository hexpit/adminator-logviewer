<?php
/**
 * @var  Arcanedev\LogViewer\Entities\Log            $log
 * @var  Illuminate\Pagination\LengthAwarePaginator  $entries
 * @var  string|null                                 $query
 */
?>

@extends('log-viewer::adminator._master')

@push('logpage-title')
    @lang('Log Dated: ') [{{ \Carbon\Carbon::parse($log->date)->format('d/m/Y') }}]
@endpush

@section('log-content')
    <div class="grid grid-cols-1 xl:grid-cols-7 gap-x-5">
        <div class="xl:col-span-1">
            {{-- Log Menu --}}
            <div>
                <h3 class="bg-gray-600 rounded-t px-5 py-3 text-lg font-black">@lang('Levels')</div>
                <div class="">
                    @foreach($log->menu() as $levelKey => $item)
                        @if ($item['count'] === 0)
                            <div>
                                <div class="flex justify-between px-5 py-3">
                                    <span class="level-{{ $levelKey }} flex items-center">
                                        <span> @include('log-viewer::adminator.icon-maker', ['icon' => $item['name'], 'size' => 6])</span>
                                        <span>{{ $item['name'] }}</span>
                                    </span>
                                    <span class="empty rounded-full p-1">{{ $item['count'] }}</span>
                                </div>
                            </div>
                        @else
                            <div>
                                <a href="{{ $item['url'] }}" class="flex justify-between">
                                    <span class="level-{{ $levelKey }} {{ $level === $levelKey ? ' active' : ''}} flex items-center">
                                         <span> @include('log-viewer::adminator.icon-maker', ['icon' => $item['name'], 'size' => 6])</span>
                                         <span>{{ $item['name'] }}</span>
                                    </span>
                                    <span class="level-{{ $levelKey }} rounded-full p-1">{{ $item['count'] }}</span>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="xl:col-span-6">
            {{-- Log Details --}}
            <div class="my-5">
                <div class="bg-gray-200 dark:bg-gray-800 flex justify-between px-5 py-3">
                    <div>@lang('Log info') :</div>
                    <div class="group-btns pull-right">
                        <a href="{{ route('log-viewer::logs.download', [$log->date]) }}" class="btn btn-sm btn-success">
                            <i class="fa fa-download"></i> @lang('Download')
                        </a>
                        <a href="#delete-log-modal" class="btn btn-sm btn-danger" data-toggle="modal">
                            <i class="fa fa-trash-o"></i> @lang('Delete')
                        </a>
                    </div>
                </div>
                <table class="w-full">
                    <tbody>
                    <tr class="odd">
                        <td class="p-2">@lang('File path') :</td>
                        <td class="p-2" colspan="7">{{ $log->getPath() }}</td>
                    </tr>
                    <tr class="even">
                        <td class="p-2">@lang('Log entries') :</td>
                        <td class="p-2">
                            <span class="bg-blue-600 rounded px-2 py-0.5">{{ $entries->total() }}</span>
                        </td>
                        <td class="p-2">@lang('Size') :</td>
                        <td class="p-2">
                            <span class="bg-blue-600 rounded px-2 py-0.5">{{ $log->size() }}</span>
                        </td>
                        <td class="p-2">@lang('Created at') :</td>
                        <td class="p-2">
                            <span class="bg-blue-600 rounded px-2 py-0.5">{{ \Carbon\Carbon::parse($log->createdAt())->format('d/m/Y h:i:s A') }}</span>
                        </td>
                        <td class="p-2">@lang('Updated at') :</td>
                        <td class="p-2">
                            <span class="bg-blue-600 rounded px-2 py-0.5">{{ \Carbon\Carbon::parse($log->updatedAt())->format('d/m/Y h:i:s A') }}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="bg-gray-200 dark:bg-gray-800">
                    {{-- Search --}}
                    <form action="{{ route('log-viewer::logs.search', [$log->date, $level]) }}" method="GET">
                        <div class="flex py-3 px-5">
                            <input id="query" name="query" class="form-control" value="{{ $query }}" placeholder="@lang('Type here to search')">
                            <div class="flex">
                                @unless (is_null($query))
                                    <a href="{{ route('log-viewer::logs.show', [$log->date]) }}" class="btn btn-secondary">
                                        (@lang(':count results', ['count' => $entries->count()])) <i class="fa fa-fw fa-times"></i>
                                    </a>
                                @endunless
                                <button id="search-btn" class="btn btn-primary">
                                    <span class="fa fa-fw fa-search"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Log Entries --}}
            <div class="my-5">
                @if ($entries->hasPages())
                    <div class="bg-gray-200 dark:bg-gray-800 flex justify-end px-5 py-3 my-2">
                        <span class="ml-auto bg-green-500 px-2 py-0.5 rounded">{{ __('Page :current of :last', ['current' => $entries->currentPage(), 'last' => $entries->lastPage()]) }}</span>
                    </div>
                @endif
                <div class="grid grid-cols-1 overflow-x-auto">
                    <table id="entries" class="w-full">
                        <thead>
                        <tr class="bg-gray-500 dark:bg-gray-800 h-12 text-left">
                            <th class="pl-2" style="width: 120px;">@lang('ENV')</th>
                            <th style="width: 120px;">@lang('Level')</th>
                            <th style="width: 120px;">@lang('Time')</th>
                            <th>@lang('Header')</th>
                            <th class="text-right" style="width: 120px;">@lang('Actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($entries as $key => $entry)
                            <?php /** @var  Arcanedev\LogViewer\Entities\LogEntry  $entry */ ?>
                            <tr class="{{ $loop->index % 2 === 0 ? 'even' : 'odd' }}">
                                <td class="p-2">
                                    <span class="env rounded px-2 py-0.5">{{ $entry->env }}</span>
                                </td>
                                <td class="p-2">
                                    <span class="level-{{ $entry->level }} rounded px-2 py-0.5">
                                        {!! $entry->level() !!}
                                    </span>
                                </td>
                                <td class="p-2">
                                    <span class="secondary">
                                        {{ $entry->datetime->format('H:i:s') }}
                                    </span>
                                </td>
                                <td class="p-2">
                                    {{ $entry->header }}
                                </td>
                                <td class="text-right p-2">
                                    @if ($entry->hasStack())
                                        <a class="btn btn-sm btn-light" role="button" data-toggle="collapse" href="#log-stack-{{ $key }}" aria-expanded="false" aria-controls="log-stack-{{ $key }}">
                                            <i class="fa fa-toggle-on"></i>
                                            @lang('Stack')
                                        </a>
                                    @endif

                                    @if ($entry->hasContext())
                                        <a class="btn btn-sm btn-light" role="button" data-toggle="collapse" href="#log-context-{{ $key }}" aria-expanded="false" aria-controls="log-context-{{ $key }}">
                                            <i class="fa fa-toggle-on"></i>
                                            @lang('Context')
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @if ($entry->hasStack() || $entry->hasContext())
                                <tr>
                                    <td colspan="5" class="stack py-0">
                                        @if ($entry->hasStack())
                                            <div class="stack-content hidden" id="log-stack-{{ $key }}">
                                                {!! $entry->stack() !!}
                                            </div>
                                        @endif

                                        @if ($entry->hasContext())
                                            <div class="stack-content hidden" id="log-context-{{ $key }}">
                                                <pre>{{ $entry->context() }}</pre>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <span class="badge badge-secondary">@lang('The list of logs is empty!')</span>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {!! $entries->appends(compact('query'))->render() !!}
        </div>
@endsection

@push('after-logscripts')
    <script>
        $(function () {
            var deleteLogForm  = $('#deleteLogForm');
            deleteLogForm.on('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure you want to permanently delete this item?',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm Permanent Delete',
                    cancelButtonText: 'Cancel',
                    icon: 'warning'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url:      $(this).attr('action'),
                            type:     $(this).attr('method'),
                            dataType: 'json',
                            data:     $('#deleteLogForm').serialize(),
                            success: function(data) {
                                if (data.result === 'success') {
                                    location.replace("{{ route('log-viewer::logs.list') }}");
                                }
                                else {
                                    alert('OOPS ! This is a lack of coffee exception !')
                                }
                            },
                            error: function(xhr, textStatus, errorThrown) {
                                alert('AJAX ERROR ! Check the console !');
                                console.error(errorThrown);
                            }
                        });
                        return false;
                    }
                });
            });




            @unless (empty(log_styler()->toHighlight()))
                @php
                    $htmlHighlight = version_compare(PHP_VERSION, '7.4.0') >= 0
                        ? join('|', log_styler()->toHighlight())
                        : join(log_styler()->toHighlight(), '|');
                @endphp
                $('.stack-content').each(function() {
                    var $this = $(this);
                    var html = $this.html().trim()
                        .replace(/({!! $htmlHighlight !!})/gm, '<strong>$1</strong>');
                    $this.html(html);
                });
            @endunless
        });
    </script>
@endpush
