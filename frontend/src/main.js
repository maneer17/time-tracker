import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'
import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(createPinia())
app.use(router)

const i18n = createI18n({
legacy: false,
  locale: 'ar',
  fallbackLocale: 'en',
  messages: {
    en: {
      mesasge: {
        hello: 'hello',
      },
      login: {
        welcome_back: 'Welcome Back',
        email: 'Email',
        password: 'Password',
        login: 'Login',
        dont_have_account: "Don't have an account?",
        sign_up_here: "Sign Up Here"
      },
      register:{
        create_account: "Create Account",
        name: "Name",
        email: "Email",
        password: "Password",
        confirm_password: "Confirm Password",
        sign_up: "Sign Up",
        you_already_have_account: "You already have an account? ",
        login: "Login"
      },
      nav: {
        home: "Home",
        about: "About",
        sign_out: "Sign Out",

      },
      addForm: {
        label:"Label",
        start_time: "Start Time",
        end_time: "End Time",
        add_entry: "Add Entry"
      },
      home: {
        history: "History",
        hide: "Hide",
        add_new_entry: "Add New Entry",
        today_entries: "Today's Entries",
        entries_of: "Time Entries of",
        error: "Oops! Error encountered",
        loading: "Loading entries...",
        error_loading_dates: "Error loading dates",
        no_dates: "No dates available"
      },
      single_entry: {
        hour: '0 hours | {n} hour | {n} hours',
        minute: '0 minutes | {n} minute | {n} minutes'
      }

    },
    ar: {
      message: {
        hello: "مرحبا",
        hide_history: 'اخفاء',
        add_entry: 'أضف نشاط جديد'
      },
      login: {
        welcome_back: 'أهلا بعودتك',
        email: 'الايميل',
        password: 'كلمة المرور',
        login:'تسجيل الدخول',
        dont_have_account: "لا تملك حساب ؟",
        sign_up_here: "أنشئ حساب هنا"
      },
      register:{
        create_account: "أنشئ حساب",
        name: "الاسم",
        email: "الايميل",
        password: "كلمة المرور",
        confirm_password: "تأكيد كلمة المرور",
        sign_up: "أنشئ حساب",
        you_already_have_account: "لديك حساب؟",
        login: "سجل الدخول"
      },
      nav: {
        home: "الصفحة الرئيسية",
        about: "عن الموقع",
        sign_out: "تسجيل الخروج",
        
      },
      addForm: {
        label:"العنوان",
        start_time: "وقت البدء",
        end_time: "وقت الانتهاء",
        add_entry: "أضف النشاط"
      },
      home: {
        history: "السجل",
        hide: "إخفاء",
        add_new_entry: "أضف نشاط جديد",
        today_entries: "نشاطات اليوم",
        entries_of: "نشاطات",
        error: "عذراً! حدث خطأ",
        loading: "جاري التحميل...",
        error_loading_dates: "خطأ في تحميل التواريخ",
        no_dates: "لا توجد تواريخ متاحة"
      },
      single_entry: {
        hour: 'صفر  ساعات | {n} ساعة | {n} ساعات',
        minute: 'صفر دقائق | {n} دقيقة | {n} دقائق'
      }      
    },
  },
});
app.use(i18n)
app.mount('#app')
