@extends('layouts.admin')
@section('title')
notifications
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title card_title_center">Add New Notifications</h3>
    </div>
    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('notifications.send')}}" method="post">
                            @csrf
                            
                            <!-- Send To Selection -->
                            <div class="form-group">
                                <label for="send_to">Send To</label>
                                <select name="send_to" id="send_to" class="form-control @if($errors->has('send_to')) is-invalid @endif" required>
                                    <option value="all" {{old('send_to') == 'all' ? 'selected' : ''}}>All Users</option>
                                    <option value="specific" {{old('send_to') == 'specific' ? 'selected' : ''}}>Specific User</option>
                                </select>
                                @if($errors->has('send_to'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('send_to') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <!-- User Selection (shown only when 'specific' is selected) -->
                            <div class="form-group" id="user_selection" style="display: none;">
                                <label for="user_id">Select User</label>
                                <select name="user_id" id="user_id" class="form-control @if($errors->has('user_id')) is-invalid @endif">
                                    <option value="">-- Select User --</option>
                                    @foreach(\App\Models\User::whereNotNull('fcm_token')->get() as $user)
                                        <option value="{{$user->id}}" {{old('user_id') == $user->id ? 'selected' : ''}}>
                                            {{$user->name}} ({{$user->email}})
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('user_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('user_id') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group mt-0">
                                <label for="title">Title</label>
                                <input type="text" class="form-control @if($errors->has('title')) is-invalid @endif" 
                                       id="title" name="title" value="{{old('title')}}" required>
                                @if($errors->has('title'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="body">Body</label>
                                <textarea name="body" id="body" 
                                          class="form-control @if($errors->has('body')) is-invalid @endif" 
                                          required>{{old('body')}}</textarea>
                                @if($errors->has('body'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('body') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="text-right mt-3">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    Send Notifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Toggle user selection based on send_to value
    document.getElementById('send_to').addEventListener('change', function() {
        var userSelection = document.getElementById('user_selection');
        var userIdField = document.getElementById('user_id');
        
        if (this.value === 'specific') {
            userSelection.style.display = 'block';
            userIdField.required = true;
        } else {
            userSelection.style.display = 'none';
            userIdField.required = false;
            userIdField.value = '';
        }
    });

    // Trigger on page load if there's an old value
    window.addEventListener('DOMContentLoaded', function() {
        var sendToSelect = document.getElementById('send_to');
        if (sendToSelect.value === 'specific') {
            document.getElementById('user_selection').style.display = 'block';
            document.getElementById('user_id').required = true;
        }
    });
</script>
@endsection