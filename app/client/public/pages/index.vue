<template>
  <Jumbotron>
    <div class="homepage_wrapper">
    <h2>Simplify Your Leave Management</h2>
    <p>
      Experience hassle-free leave management with LeaveSync—your all-in-one solution for managing time-off requests, <br>streamlining approvals, and tracking leave effortlessly.
    </p>
    
    <ul>
      <li>✅ Easy leave requests</li>
      <li>✅ Automated holiday & weekend exclusion</li>
      <li>✅ Instant approvals & notifications</li>
    </ul>

    <a
      href="https://docs.google.com/document/d/1onPg8l5NdO5NSReJKt0qOC0gyHgefN-IPg943Wo2eEw/edit?usp=sharing"
      target="_blank"
    >
      Learn more & get started
    </a>
  </div>
  </Jumbotron>
</template>

<style lang="scss">
.homepage_wrapper {
  font-family: 'Poppins', sans-serif, 'bold';
  margin-top: -80px;
  color: black;
  text-align: center;

  @media screen and (max-width: 456px) {
    margin-top: 40px;
  }
  
  & > h2 {
    margin-bottom: 12px;
    font-size: 54px;

    @media screen and (max-width: 456px) {
      font-size: 32px;
      margin-bottom: 30px;
      font-weight: 500;
      }
  }

  & > p {
    margin-bottom: 40px;
    font-size: 24px;

    @media screen and (max-width: 456px) {
        font-size: 18px;
      }
  }

  & > ul {
    padding-left: 0;
    list-style: none;
    > li {
      font-size: 20px;

      @media screen and (max-width: 456px) {
        font-size: 16px;
      }
    }
  }

  & > a {
    font-size: 16px;
    @media screen and (max-width: 456px) {
        font-size: 14px;
      }
  }
}

</style>