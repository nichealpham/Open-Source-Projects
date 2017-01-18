"use strict";

const conversions = require("webidl-conversions");
const utils = require("./utils.js");
const impl = utils.implSymbol;

function DOMPointReadOnly() {
  const args = [];
  for (let i = 0; i < arguments.length && i < 4; ++i) {
    args[i] = utils.tryImplForWrapper(arguments[i]);
  }
  if (args[0] !== undefined) {
  args[0] = conversions["unrestricted double"](args[0]);
  }
  if (args[1] !== undefined) {
  args[1] = conversions["unrestricted double"](args[1]);
  }
  if (args[2] !== undefined) {
  args[2] = conversions["unrestricted double"](args[2]);
  }
  if (args[3] !== undefined) {
  args[3] = conversions["unrestricted double"](args[3]);
  }

  module.exports.setup(this, args);
}


DOMPointReadOnly.fromPoint = function fromPoint() {
  const args = [];
  for (let i = 0; i < arguments.length && i < 1; ++i) {
    args[i] = utils.tryImplForWrapper(arguments[i]);
  }
  return utils.tryWrapperForImpl(Impl.fromPoint.apply(Impl, args));
};

DOMPointReadOnly.prototype.toString = function () {
  if (this === DOMPointReadOnly.prototype) {
    return "[object DOMPointReadOnlyPrototype]";
  }
  return this[impl].toString();
};
Object.defineProperty(DOMPointReadOnly.prototype, "x", {
  get() {
    return utils.tryWrapperForImpl(this[impl].x);
  },
  enumerable: true,
  configurable: true
});

Object.defineProperty(DOMPointReadOnly.prototype, "y", {
  get() {
    return utils.tryWrapperForImpl(this[impl].y);
  },
  enumerable: true,
  configurable: true
});

Object.defineProperty(DOMPointReadOnly.prototype, "z", {
  get() {
    return utils.tryWrapperForImpl(this[impl].z);
  },
  enumerable: true,
  configurable: true
});

Object.defineProperty(DOMPointReadOnly.prototype, "w", {
  get() {
    return utils.tryWrapperForImpl(this[impl].w);
  },
  enumerable: true,
  configurable: true
});


module.exports = {
  mixedInto: [],
  is(obj) {
    if (obj) {
      if (obj[impl] instanceof Impl.implementation) {
        return true;
      }
      for (let i = 0; i < module.exports.mixedInto.length; ++i) {
        if (obj instanceof module.exports.mixedInto[i]) {
          return true;
        }
      }
    }
    return false;
  },
  isImpl(obj) {
    if (obj) {
      if (obj instanceof Impl.implementation) {
        return true;
      }

      const wrapper = utils.wrapperForImpl(obj);
      for (let i = 0; i < module.exports.mixedInto.length; ++i) {
        if (wrapper instanceof module.exports.mixedInto[i]) {
          return true;
        }
      }
    }
    return false;
  },
  create(constructorArgs, privateData) {
    let obj = Object.create(DOMPointReadOnly.prototype);
    this.setup(obj, constructorArgs, privateData);
    return obj;
  },
  createImpl(constructorArgs, privateData) {
    let obj = Object.create(DOMPointReadOnly.prototype);
    this.setup(obj, constructorArgs, privateData);
    return utils.implForWrapper(obj);
  },
  _internalSetup(obj) {
  },
  setup(obj, constructorArgs, privateData) {
    if (!privateData) privateData = {};
    privateData.wrapper = obj;

    this._internalSetup(obj);

    obj[impl] = new Impl.implementation(constructorArgs, privateData);
    obj[impl][utils.wrapperSymbol] = obj;
  },
  interface: DOMPointReadOnly,
  expose: {
    Window: { DOMPointReadOnly: DOMPointReadOnly },
    Worker: { DOMPointReadOnly: DOMPointReadOnly }
  }
};


const Impl = require("../nodes/DOMPointReadOnly-impl.js");
