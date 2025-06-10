export default {
  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      fieldErrors: {}
    };
  },
  methods: {
    async login() {
      this.fieldErrors = {};
      try {
        const response = await axios.post('/login', this.form);
        if (response.status === 200) {
          window.location.href = '/home';
        }
      } catch (error) {
        if (error.response && error.response.data) {
          if (error.response.data.errors) {
            this.fieldErrors = error.response.data.errors;
          } else if (error.response.data.error) {
            this.fieldErrors.general = error.response.data.error;
          } else {
            this.fieldErrors.general = 'Invalid credentials';
          }
        } else {
          this.fieldErrors.general = 'An error occurred. Please try again.';
        }
      }
    },
  },
};