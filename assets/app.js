import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

const THEME_STORAGE_KEY = 'serinity-theme';
const DARK_THEME_VALUE = 'dark';
const LIGHT_THEME_VALUE = 'light';

const getStoredTheme = () => {
	try {
		return window.localStorage.getItem(THEME_STORAGE_KEY);
	} catch (error) {
		return null;
	}
};

const getPreferredTheme = () => {
	const storedTheme = getStoredTheme();

	if (storedTheme === DARK_THEME_VALUE || storedTheme === LIGHT_THEME_VALUE) {
		return storedTheme;
	}

	return window.matchMedia('(prefers-color-scheme: dark)').matches
		? DARK_THEME_VALUE
		: LIGHT_THEME_VALUE;
};

const applyTheme = (theme) => {
	document.documentElement.setAttribute('data-theme', theme);
};

const saveTheme = (theme) => {
	try {
		window.localStorage.setItem(THEME_STORAGE_KEY, theme);
	} catch (error) {
		// Ignore storage errors (private mode, blocked storage).
	}
};

applyTheme(getPreferredTheme());

document.addEventListener('DOMContentLoaded', () => {
	const themeToggle = document.getElementById('nightModeToggle');
	const updateThemeButton = () => {
		if (!themeToggle) {
			return;
		}

		const darkEnabled = document.documentElement.getAttribute('data-theme') === DARK_THEME_VALUE;
		themeToggle.textContent = darkEnabled ? 'Day mode' : 'Night mode';
		themeToggle.setAttribute('aria-pressed', darkEnabled ? 'true' : 'false');
		themeToggle.setAttribute('aria-label', darkEnabled ? 'Enable day mode' : 'Enable night mode');
	};

	if (themeToggle) {
		updateThemeButton();

		themeToggle.addEventListener('click', () => {
			const darkEnabled = document.documentElement.getAttribute('data-theme') === DARK_THEME_VALUE;
			const nextTheme = darkEnabled ? LIGHT_THEME_VALUE : DARK_THEME_VALUE;
			applyTheme(nextTheme);
			saveTheme(nextTheme);
			updateThemeButton();
		});
	}

	const widget = document.getElementById('floatingNotify');
	const button = document.getElementById('floatingNotifyBtn');
	const panel = document.getElementById('floatingNotifyPanel');

	if (!widget || !button || !panel) {
		return;
	}

	const closePanel = () => {
		widget.classList.remove('is-open');
		button.setAttribute('aria-expanded', 'false');
	};

	const openPanel = () => {
		widget.classList.add('is-open');
		button.setAttribute('aria-expanded', 'true');
	};

	closePanel();

	button.addEventListener('click', (event) => {
		event.preventDefault();

		if (!widget.classList.contains('is-open')) {
			openPanel();
			return;
		}

		closePanel();
	});

	document.addEventListener('click', (event) => {
		if (!widget.classList.contains('is-open')) {
			return;
		}

		if (!widget.contains(event.target)) {
			closePanel();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			closePanel();
		}
	});
});
