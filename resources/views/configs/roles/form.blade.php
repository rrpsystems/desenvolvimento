    
<div class="form-row">
    <div class="table-responsive">
        <table class="table no-wrap table-sm table-striped table-valign-middle">
            <thead class="thead-dark">
                <tr>
                    <th></th>
                    <th>Permissão</th>
                    <th class="text-center">Listar</th>
                    <th class="text-center">Cadastrar</th>
                    <th class="text-center">Editar</th>
                    <th class="text-center">Excluir</th>
                </tr>
            </thead>
                <tr class="bg-primary"><td colspan="6">Configurações:</td></tr>

                @forelse( $permissions as $name => $permission)
                    @isset($roles)
                        @continue( $roles->name == 'Root')
                    @endisset
                    <tr>
                        <td class="text-center"></td>
                        <td>
                        @lang("messages.$name")
                        </td>
                        <td class="text-center">
                            @foreach($permission as $per)
                                @continue($per->name == $name.'-create' )
                                @continue($per->name == $name.'-edit' )
                                @continue($per->name == $name.'-delete' )
                                @if( $per->name == $name.'-list' )
                                    <div class="icheck-primary">
                                        <input  id="{{ $per->name }}" value="{{ $per->name }}" type="checkbox" name="permission[]" {{ $selected->get($per->name) }}>
                                        <label for="{{ $per->name }}"></label>
                                    </div>
                                @else
                                    <div class="icheck-primary">
                                        <input  id="users-empty" type="checkbox" name="permission[]" disabled>
                                        <label for="users-empty"></label>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                   
                        <td class="text-center">
                            @foreach($permission as $per)
                                @continue($per->name == $name.'-list' )
                                @continue($per->name == $name.'-edit' )
                                @continue($per->name == $name.'-delete' )
                                @if( $per->name == $name.'-create' )
                                    <div class="icheck-success">
                                        <input  id="{{ $per->name }}" value="{{ $per->name }}" type="checkbox" name="permission[]" {{ $selected->get($per->name) }}>
                                        <label for="{{ $per->name }}"></label>
                                    </div>
                                @else
                                    <div class="icheck-primary">
                                        <input  id="users-empty" type="checkbox" name="permission[]" disabled>
                                        <label for="users-empty"></label>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                    
                        <td class="text-center">
                            @foreach($permission as $per)
                                @continue($per->name == $name.'-list' )
                                @continue($per->name == $name.'-create' )
                                @continue($per->name == $name.'-delete' )
                                @if( $per->name == $name.'-edit' )
                                    <div class="icheck-warning">
                                        <input  id="{{ $per->name }}" value="{{ $per->name }}" type="checkbox" name="permission[]" {{ $selected->get($per->name) }}>
                                        <label for="{{ $per->name }}"></label>
                                    </div>
                                @else
                                    <div class="icheck-primary">
                                        <input  id="users-empty" type="checkbox" name="permission[]" disabled>
                                        <label for="users-empty"></label>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                    
                        <td class="text-center">
                            @foreach($permission as $per)
                                @continue($per->name == $name.'-list' )
                                @continue($per->name == $name.'-create' )
                                @continue($per->name == $name.'-edit' )
                                    @if( $per->name == $name.'-delete' )
                                        <div class="icheck-danger">
                                            <input  id="{{ $per->name }}" value="{{ $per->name }}" type="checkbox" name="permission[]" {{ $selected->get($per->name) }} >
                                            <label for="{{ $per->name }}"></label>
                                        </div>
                                    @else
                                        <div class="icheck-primary">
                                            <input  id="users-empty" type="checkbox" name="permission[]" disabled>
                                            <label for="users-empty"></label>
                                        </div>
                                    @endif
                            @endforeach
                            
                        </td>
                                                       
                    </tr>
                    
                @empty
                    sem dados
                @endforelse
                
            </tbody>
        </table>
    </div>
</div>


@section('css')
<link rel="stylesheet" href="{{ asset('vendor/css/icheck/icheck-bootstrap.min.css') }}">
@stop

