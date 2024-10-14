<template>
  <section class="payment">
    <div>
      <span>Amount:</span>
      <input type="text" v-model="amount" />
      <button type="button" @click="razorPay">Pay with Razorpay</button>
    </div>
  </section>
</template>