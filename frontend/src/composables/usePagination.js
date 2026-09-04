import { ref, computed } from 'vue'
import useApi from './useApi'

export function usePagination(apiFunc, options = {}) {
    const { filters = {}, initialPage = 1, immediate = true } = options

    function resolveFilters() {
        return filters?.value ?? filters
        // filters could be a ref or a plain object this is way we handle it this way
    }

    const paginatorData = ref(null)
    //this is the full raw Laravel response object.

    const { loading, error, request } = useApi(
        (page) => apiFunc(page, resolveFilters()).then(response => {
            paginatorData.value = response.data
            return response  // ← useApi does result.data on this, so it takes out the pagination attributes which we needs 
        }),
        false
    )

    const items   = computed(() => paginatorData.value?.data ?? []) // array of actual data from the res
    const isEmpty = computed(() => !loading.value && items.value.length === 0)

    const goToPage = (page) => request(page)
    const refresh  = () => request(1)

    if (immediate) request(initialPage)

    return { items, paginatorData, loading, error, isEmpty, goToPage, refresh }
}