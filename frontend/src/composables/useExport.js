import { ref } from "vue";
const useExport = () => {
    const loading = ref(false);
    const error = ref(null);
const download = async (serviceFn, params, filename) => {
    loading.value = true;
    error.value = null;
    try {
        const response = await serviceFn(params); 
        const blobUrl = URL.createObjectURL(response.data);
        const a = document.createElement('a');
        a.href = blobUrl;
        a.download = `${filename}.${params.file_extension ?? 'pdf'}`;
        a.click();
        URL.revokeObjectURL(blobUrl);
    } catch (err) {
        error.value = err.message;
    } finally {
        loading.value = false;
    }
};

    return { loading, error, download }; // ← this belongs to useExport
};

export default useExport;