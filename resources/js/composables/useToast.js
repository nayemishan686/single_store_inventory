import { reactive } from "vue";

const state = reactive({
    list: [], // { id, type:'success'|'error', text }
});

export function useToast() {
    function push(type, text, ttl = 3000) {
        const id = Date.now() + Math.random();
        state.list.push({ id, type, text });
        setTimeout(() => remove(id), ttl);
    }
    function remove(id) {
        const i = state.list.findIndex((t) => t.id === id);
        if (i !== -1) state.list.splice(i, 1);
    }
    return {
        toasts: state.list,
        success: (t, ttl) => push("success", t, ttl),
        error: (t, ttl) => push("error", t, ttl),
        remove,
    };
}
