@extends('layouts.page')
@section('content')
	<div id="signup-page" class="row">
		<div class="animated fadeInRight col-md-6 col-md-push-6">
			<div class="row heart-row">
				<div class="col-xs-4"></div>
				<div class="col-xs-4">
					<span class="heart-container"><i class="fa fa-heart fa-5x"></i></span>
				</div>
				<div class="col-xs-4"></div>
			</div>
			<div class="signup-advantages">
				<ul>
					{{ Lang::get('auth.signupAdvantages') }}
				</ul>
			</div>
		</div>

		<div class="animated fadeInLeft col-md-6 col-md-pull-6">
			<h1><i class="fa fa-user"></i>{{ Lang::get('auth.createYourAccount')}}</h1>
			<div id="signup-text">
				{{Lang::get('auth.signupText')}}
			</div>
			{{ Form::open(['url' => URL::route('users.store'), 'class' => 'form-horizontal']) }}

				<!-- Login -->
				<div class="form-group {{{ $errors->has('login') ? 'error' : '' }}}">
					{{ Form::label('login', Lang::get('auth.login'), ['class' => 'col-sm-2 control-label']) }}

					<div class="col-sm-10">
						{{ Form::text('login', Input::old('login'), ['class' => 'form-control', 'id' => 'login-signup', 'placeholder' => 'janedoe']) }}
						<div id="login-validator">

						</div>
						@if ( ! empty($errors->first('login')))
							<div id="login-error">
								{{ TextTools::warningTextForm($errors->first('login')) }}
							</div>
						@endif
					</div>
				</div>

				<!-- Email address -->
				<div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
					{{ Form::label('email', Lang::get('auth.emailAddress'), ['class' => 'col-sm-2 control-label']) }}

					<div class="col-sm-10">
						{{ Form::email('email', Input::old('email'), ['class' => 'form-control', 'id' => 'email-signup', 'placeholder' => 'jane@example.com']) }}
						<div id="respect-privacy">
							{{ Lang::get('auth.carefulPrivacy') }}
						</div>
						@if ( ! empty($errors->first('email')))
							<div id="email-error">
								{{ TextTools::warningTextForm($errors->first('email')) }}
							</div>
						@endif
					</div>
				</div>

				<!-- Password -->
				<div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
					{{ Form::label('password', Lang::get('auth.password'), ['class' => 'col-sm-2 control-label']) }}

					<div class="col-sm-10">
						{{ Form::password('password', ['class' => 'form-control', 'id' => 'password']) }}
						@if ( ! empty($errors->first('password')))
							{{ TextTools::warningTextForm($errors->first('password')) }}
						@endif
					</div>
				</div>

				<!-- Submit button -->
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						{{ Form::submit(Lang::get('auth.signupButton'), ['class' => 'transition animated fadeInUp btn btn-primary btn-lg', 'id' => 'submit-form']) }}
					</div>
				</div>
			</div>


		{{ Form::close() }}
	</div>
@stop
