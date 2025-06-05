import { ref, computed } from 'vue';

const user = ref(null);
const profileImage = ref('/images/default-profile.png');
const editForm = ref({
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const isDark = computed(() => document.documentElement.classList.contains('dark'));

function cancelEdit() {
  if (user.value) {
    editForm.value.username = user.value.username;
    editForm.value.email = user.value.email;
    editForm.value.password = '';
    editForm.value.password_confirmation = '';
  }
  window.location.href = '/profile';
}

async function saveProfile() {
  if (editForm.value.password && editForm.value.password !== editForm.value.password_confirmation) {
    alert('Passwords do not match!');
    return;
  }
  try {
    const payload = {
      username: editForm.value.username,
      email: editForm.value.email,
    };
    if (editForm.value.password) {
      payload.password = editForm.value.password;
      payload.password_confirmation = editForm.value.password_confirmation;
    }
    const response = await fetch('/api/user/profile', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify(payload),
    });
    if (!response.ok) {
      const data = await response.json();
      alert(data.message || 'Failed to update profile.');
      return;
    }
    window.location.href = '/profile';
  } catch (error) {
    alert('An error occurred while updating profile.');
  }
}

async function fetchUserProfile() {
  try {
    const response = await fetch('/api/user-session');
    if (response.ok) {
      const data = await response.json();
      user.value = data.user;
      editForm.value.username = data.user.username;
      editForm.value.email = data.user.email;
      // Optionally set profileImage.value if user has a custom image
    }
  } catch (e) {
    user.value = null;
  }
}

export default function useEditProfile() {
  return {
    user,
    profileImage,
    editForm,
    isDark,
    cancelEdit,
    saveProfile,
    fetchUserProfile
  };
}
