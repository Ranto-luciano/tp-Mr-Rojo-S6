document.addEventListener('DOMContentLoaded', () => {
	const forms = document.querySelectorAll('form[role="search"], .search-panel');

	forms.forEach((form) => {
		form.addEventListener('submit', (event) => {
			const input = form.querySelector('input[type="search"]');
			if (!input) {
				return;
			}

			input.value = input.value.trim();
			if (input.value === '') {
				event.preventDefault();
			}
		});
	});
});
