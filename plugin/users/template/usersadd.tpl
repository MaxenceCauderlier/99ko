<section>
	<header>{{Lang.users-add}}</header>
	<form method="POST" action="{{link}}">
		{{SHOW.tokenField}}
		<label for="email">{{ Lang.users-mail}}</label>
		<input type="email" id="email" name="email" required />
		<label for="pwd">{{Lang.password}}</label>
		<input type="password" id="pwd" name="pwd" required />
		<button>{{Lang.submit}}</button>
	</form>
</section>
