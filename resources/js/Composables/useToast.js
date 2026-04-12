import { ref } from 'vue';

const toasts = ref([]);

export function useToast() {
  const addToast = (message, type = 'success', duration = 3000) => {
    const id = Date.now();
    toasts.value.push({ id, message, type });
    
    setTimeout(() => {
      removeToast(id);
    }, duration);
  };

  const removeToast = (id) => {
    toasts.value = toasts.value.filter(t => t.id !== id);
  };

  return { toasts, addToast, removeToast };
}

// Global accessor for non-setup contexts
if (typeof window !== 'undefined') {
    window.toast = (message, type = 'success') => {
        const { addToast } = useToast();
        addToast(message, type);
    };
}
