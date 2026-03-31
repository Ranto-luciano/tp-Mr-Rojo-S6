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

	const carousels = document.querySelectorAll('[data-carousel]');
	carousels.forEach((carousel) => {
		const track = carousel.querySelector('[data-carousel-track]');
		const prev = carousel.querySelector('[data-carousel-prev]');
		const next = carousel.querySelector('[data-carousel-next]');
		const firstItem = track?.querySelector('.gallery-item');

		if (!track || !prev || !next || !firstItem) {
			return;
		}

		const step = () => firstItem.getBoundingClientRect().width + 12;

		const updateButtons = () => {
			const maxScroll = track.scrollWidth - track.clientWidth - 1;
			prev.disabled = track.scrollLeft <= 1;
			next.disabled = track.scrollLeft >= maxScroll;
		};

		prev.addEventListener('click', () => {
			track.scrollBy({ left: -step(), behavior: 'smooth' });
		});

		next.addEventListener('click', () => {
			track.scrollBy({ left: step(), behavior: 'smooth' });
		});

		track.addEventListener('scroll', updateButtons, { passive: true });
		window.addEventListener('resize', updateButtons);
		updateButtons();
	});
});
