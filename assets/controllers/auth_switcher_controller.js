import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['signup', 'signin', 'signupTab', 'signinTab'];

  connect() {
    if (this.signinTabTarget.classList.contains('is-active')) {
      this.activateSignin(false);
    } else {
      this.activateSignup(false);
    }
  }

  showSignin(event) {
    if (event) event.preventDefault();
    this.activateSignin(true);
  }

  showSignup(event) {
    if (event) event.preventDefault();
    this.activateSignup(true);
  }

  activateSignin(animate = true) {
    this.updateTabs(this.signinTabTarget, this.signupTabTarget);
    this.togglePanels(this.signupTarget, this.signinTarget, animate);
  }

  activateSignup(animate = true) {
    this.updateTabs(this.signupTabTarget, this.signinTabTarget);
    this.togglePanels(this.signinTarget, this.signupTarget, animate);
  }

  updateTabs(activeTab, inactiveTab) {
    activeTab.classList.add('is-active');
    activeTab.setAttribute('aria-selected', 'true');

    inactiveTab.classList.remove('is-active');
    inactiveTab.setAttribute('aria-selected', 'false');
  }

  togglePanels(hidePanel, showPanel, animate) {
    clearTimeout(this.switchTimeout);

    if (!animate) {
      hidePanel.style.display = 'none';
      hidePanel.classList.remove('is-active', 'ac-fade-in', 'ac-fade-out');

      showPanel.style.display = 'block';
      showPanel.classList.remove('ac-fade-out');
      showPanel.classList.add('is-active');

      return;
    }

    hidePanel.classList.remove('ac-fade-in');
    hidePanel.classList.add('ac-fade-out');

    this.switchTimeout = setTimeout(() => {
      hidePanel.style.display = 'none';
      hidePanel.classList.remove('is-active', 'ac-fade-out');

      showPanel.style.display = 'block';
      showPanel.classList.remove('ac-fade-out');
      showPanel.classList.add('is-active', 'ac-fade-in');

      const firstInput = showPanel.querySelector('input, select, textarea');
      if (firstInput) firstInput.focus();

      setTimeout(() => {
        showPanel.classList.remove('ac-fade-in');
      }, 300);
    }, 300);
  }
}