const { createApp, ref } = Vue

// Définiton du menu
const navigation = [
  { name: 'Entreprises', href: '/companies', current: true },
  { name: 'Produits', href: '/products', current: false },
  { name: 'Fournisseurs', href: '/providers', current: false },
  { name: 'Clients', href: '/clients', current: false },
  { name: 'Transactions', href: '/transactions', current: false },
  { name: 'Employés', href: '/employees', current: false },
]

// Horloge
function getNow(){
    const today = new Date();
    const time = (today.getHours() < 10? '0' : '') + today.getHours() + ":" + (today.getMinutes() < 10? '0' : '') + today.getMinutes();
    return time;
}

createApp({
    data(){
        const sidebarOpen = ref(false);

        // Déterminer la page active
        Object.keys(navigation).forEach(key => {
            if(navigation[key]['href'] == window.location.pathname)
            {
              navigation[key]['current'] = true;
            }
            else
            {
              navigation[key]['current'] = false;
            }
        });

        var timestamp = getNow();
        return {
          navigation,
          sidebarOpen,
          timestamp,
          user_session,
        }
    },
    mixins: [mixin_table_filler, mixin_manage_modal],
    methods: {
        getNow: function() {
            this.timestamp = getNow();
        }
    },
    async mounted() {
        setInterval(this.getNow, 1000);
    },

}).mount('#app');
