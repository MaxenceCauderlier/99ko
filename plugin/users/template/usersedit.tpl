<section>
	<header>{{Lang.users-edit}}</header>
	<form method="POST" action="{{link}}">
		{{SHOW.tokenField}}
		<input type="hidden" name="id" value="{{user.id}}" />
		<label for="email">{{ Lang.users-mail}}</label>
		<input type="email" id="email" name="email" value="{{user.email}}" required />
		<label for="pwd">{{Lang.password}}</label>
		<input type="password" id="pwd" name="pwd" />
		<button>{{Lang.submit}}</button>
	</form>
</section>
