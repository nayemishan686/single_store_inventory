import { ref, onMounted } from "vue";

const theme = ref("light"); // 'light' | 'dark'

export function useTheme() {
    const setTheme = (t) => {
        theme.value = t;
        const root = document.documentElement;
        if (t === "dark") root.classList.add("dark");
        else root.classList.remove("dark");
        localStorage.setItem("theme", t);
    };

    const toggle = () => setTheme(theme.value === "dark" ? "light" : "dark");

    onMounted(() => {
        const saved = localStorage.getItem("theme");
        if (saved === "dark" || saved === "light") setTheme(saved);
        else {
            // system preference optional:
            const prefersDark = window.matchMedia(
                "(prefers-color-scheme: dark)"
            ).matches;
            setTheme(prefersDark ? "dark" : "light");
        }
    });

    return { theme, setTheme, toggle };
}
