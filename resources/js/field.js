Nova.booting((Vue, router, store) => {
  Vue.component('index-nova-belongs-to-morph-many', Vue.component('index-belongs-to-field'))
  Vue.component('detail-nova-belongs-to-morph-many', Vue.component('detail-belongs-to-field'))
  Vue.component(
      'form-nova-belongs-to-morph-many',
      Vue.component('form-belongs-to-field').extend(require('./components/FormField'))
  )
})
