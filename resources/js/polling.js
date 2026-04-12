import { onMounted, onBeforeUnmount } from "vue";

export function usePolling(callback, interval = 5000) {
  let timer;

  onMounted(() => {
    callback(); // initial fetch
    timer = setInterval(callback, interval);
  });

  onBeforeUnmount(() => {
    clearInterval(timer);
  });
}