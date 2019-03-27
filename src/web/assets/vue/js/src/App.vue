<template>
  <router-view></router-view>
</template>

<script>
import './tailwind.css'

export default {
  name: 'App',
  mounted() {
    const navMenuLinks = document.querySelectorAll('#nav-commerceinsights .subnav a')
    navMenuLinks.forEach(link => {
      link.onclick = ev => {
        ev.preventDefault();
        const parts = link.href.split('/')
        const route = parts[parts.length - 1]
        this.$router.push(route)

        document.querySelector('#nav-commerceinsights .subnav a.sel').classList.toggle('sel')
        link.classList.toggle('sel')
      }
    })
  }
}
</script>

<style scoped></style>
