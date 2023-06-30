{{--@extends('layouts.main')--}}
@extends('layouts.app', ['page' => __('Соц. сети'), 'pageSlug' => 'countries'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <h4 class="card-title"> Список соц. сетей</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table tablesorter " id="">
                            <thead class=" text-primary">
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">RU</th>
                                <th class="text-center">EN</th>
                                <th class="text-center">Short</th>
                                <th class="text-center">Icon</th>
                                <th class="text-center">Добалено</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($socials as $social)
                                <tr>
                                    <td class="text-center">{{ $social->id }}</td>
                                    <td class="text-center">{{ $social->name_ru }}</td>
                                    <td class="text-center">{{ $social->name_en }}</td>
                                    <td class="text-center">{{ $social->short_name }}</td>
                                    <td class="text-center"><img src={{ $social->image }} width="24"></td>
                                    <td class="text-center">{{ $social->created_at }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex">
                        {!! $socials->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

