import {ref, computed} from 'vue';
const usePagination = (fetchFn)=>{
    const items = ref([])
    const currentPage = ref(1);
    const lastPage = ref(1);
    const loading = ref(false)
    const error = ref(null)
    const hasMore = computed(() => currentPage.value < lastPage.value)

}


const loadMore = ()=>{
    currentPage+=1;
    fetchFn();
    comments.value = [...comments.value, ...data]
}
const hasMore = computed(() => currentPage.value < lastPage.value)