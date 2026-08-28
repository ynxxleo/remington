<template>
  <div class="tokenicon-wrap">
    <img v-if="!failed" class="tokenicon-image" @error="imageError" :src="image" :alt="alt" />
    <span v-else class="tokenicon-fallback" :aria-label="alt">{{ initials }}</span>
  </div>
</template>

<script>
// component
export default {

  data() {
    return { failed: false };
  },

  computed: {
    initials() {
      return String(this.alt || '?').replace(/[^a-z0-9]/gi, '').slice(0, 3).toUpperCase() || '?';
    },
  },

  // component props
  props: {
    image: { type: String, default: '', required: true },
    alt: { type: String, default: 'ICON' },
  },

  // custom mounted
  methods: {

    // handler for token images that don't exist
    imageError( e ) {
      this.failed = true;
    },
  },
}
</script>

<style lang="scss">
$iconSize: 46px;
// info colors
$colorInfo: darken( slategray, 15% );
$colorInfoText: lighten( $colorInfo, 45% );

// comp wrapper
.tokenicon-wrap {
  display: block;
  position: relative;
  width: $iconSize;
  min-height: $iconSize;

  .tokenicon-image {
    display: block;
    position: relative;
    overflow: hidden;
    text-align: center;
    width: $iconSize;
    height: auto;

    &.default {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: center;
      color: $colorInfoText;
      background-color: $colorInfo;
      border-radius: 100%;
      height: $iconSize;
      line-height: $iconSize;
      letter-spacing: -1px;
      transform: rotate( -25deg );
    }
  }

  .tokenicon-fallback {
    display: flex;
    align-items: center;
    justify-content: center;
    width: $iconSize;
    height: $iconSize;
    border: 1px solid rgba(32, 225, 154, .35);
    border-radius: 50%;
    color: #20e19a;
    background: linear-gradient(145deg, #18241f, #0b1210);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .02em;
  }
}
</style>
