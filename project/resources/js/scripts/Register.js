export default {
  data() {
    return {
      form: {
        username: '',
        email: '',
        password: '',
        confirmPassword: '',
      },
      fieldErrors: {}
    };
  },
  methods: {
    async register() {
      this.fieldErrors = {};
      if (this.form.password !== this.form.confirmPassword) {
        this.fieldErrors.password_confirmation = 'Passwords do not match!';
        return;
      }
      try {
        await axios.post('/register', {
          username: this.form.username,
          email: this.form.email,
          password: this.form.password,
          password_confirmation: this.form.confirmPassword,
        });
        window.location.href = '/home';
      } catch (error) {
        if (error.response && error.response.data) {
          if (error.response.data.errors) {
            this.fieldErrors = error.response.data.errors;
          } else if (error.response.data.message) {
            this.fieldErrors.general = error.response.data.message;
          } else {
            this.fieldErrors.general = 'An error occurred during registration.';
          }
        } else {
          this.fieldErrors.general = 'An error occurred. Please try again.';
        }
      }
    },
  },
};
