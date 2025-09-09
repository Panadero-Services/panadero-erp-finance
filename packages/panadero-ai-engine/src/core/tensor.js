/**
 * Tensor Class - Core mathematical operations
 * 
 * Implements basic tensor operations for neural network computations
 * Optimized for JavaScript performance
 */

class Tensor {
  constructor(data, shape = null) {
    this.data = data;
    this.shape = shape || this._inferShape(data);
    this.size = this._calculateSize();
  }

  /**
   * Infer shape from data
   * @param {Array} data - Input data
   * @returns {Array} Shape array
   */
  _inferShape(data) {
    if (!Array.isArray(data)) {
      return [];
    }
    
    const shape = [];
    let current = data;
    
    while (Array.isArray(current)) {
      shape.push(current.length);
      current = current[0];
    }
    
    return shape;
  }

  /**
   * Calculate total size
   * @returns {number} Total number of elements
   */
  _calculateSize() {
    return this.shape.reduce((acc, dim) => acc * dim, 1);
  }

  /**
   * Matrix multiplication
   * @param {Tensor} other - Other tensor
   * @returns {Tensor} Result tensor
   */
  matmul(other) {
    if (this.shape.length !== 2 || other.shape.length !== 2) {
      throw new Error('Matrix multiplication requires 2D tensors');
    }
    
    if (this.shape[1] !== other.shape[0]) {
      throw new Error('Incompatible dimensions for matrix multiplication');
    }

    const result = [];
    for (let i = 0; i < this.shape[0]; i++) {
      result[i] = [];
      for (let j = 0; j < other.shape[1]; j++) {
        let sum = 0;
        for (let k = 0; k < this.shape[1]; k++) {
          sum += this.data[i][k] * other.data[k][j];
        }
        result[i][j] = sum;
      }
    }

    return new Tensor(result);
  }

  /**
   * Element-wise addition
   * @param {Tensor|number} other - Other tensor or scalar
   * @returns {Tensor} Result tensor
   */
  add(other) {
    if (typeof other === 'number') {
      return this._scalarOperation(other, (a, b) => a + b);
    }
    
    if (!this._sameShape(other)) {
      throw new Error('Tensors must have the same shape for addition');
    }
    
    return this._elementWiseOperation(other, (a, b) => a + b);
  }

  /**
   * Element-wise multiplication
   * @param {Tensor|number} other - Other tensor or scalar
   * @returns {Tensor} Result tensor
   */
  mul(other) {
    if (typeof other === 'number') {
      return this._scalarOperation(other, (a, b) => a * b);
    }
    
    if (!this._sameShape(other)) {
      throw new Error('Tensors must have the same shape for multiplication');
    }
    
    return this._elementWiseOperation(other, (a, b) => a * b);
  }

  /**
   * Transpose tensor
   * @returns {Tensor} Transposed tensor
   */
  transpose() {
    if (this.shape.length !== 2) {
      throw new Error('Transpose requires 2D tensor');
    }
    
    const result = [];
    for (let i = 0; i < this.shape[1]; i++) {
      result[i] = [];
      for (let j = 0; j < this.shape[0]; j++) {
        result[i][j] = this.data[j][i];
      }
    }
    
    return new Tensor(result);
  }

  /**
   * Reshape tensor
   * @param {Array} newShape - New shape
   * @returns {Tensor} Reshaped tensor
   */
  reshape(newShape) {
    const newSize = newShape.reduce((acc, dim) => acc * dim, 1);
    if (newSize !== this.size) {
      throw new Error('New shape must have the same total size');
    }
    
    const flatData = this._flatten();
    const reshapedData = this._reshapeArray(flatData, newShape);
    
    return new Tensor(reshapedData, newShape);
  }

  /**
   * Flatten tensor to 1D
   * @returns {Array} Flattened data
   */
  _flatten() {
    const result = [];
    this._flattenRecursive(this.data, result);
    return result;
  }

  /**
   * Recursive flatten helper
   * @param {Array} arr - Array to flatten
   * @param {Array} result - Result array
   */
  _flattenRecursive(arr, result) {
    for (let i = 0; i < arr.length; i++) {
      if (Array.isArray(arr[i])) {
        this._flattenRecursive(arr[i], result);
      } else {
        result.push(arr[i]);
      }
    }
  }

  /**
   * Reshape array to new shape
   * @param {Array} data - Flat data
   * @param {Array} shape - Target shape
   * @returns {Array} Reshaped array
   */
  _reshapeArray(data, shape) {
    if (shape.length === 1) {
      return data;
    }
    
    const result = [];
    let index = 0;
    
    const buildArray = (dims) => {
      if (dims.length === 1) {
        const slice = data.slice(index, index + dims[0]);
        index += dims[0];
        return slice;
      }
      
      const arr = [];
      for (let i = 0; i < dims[0]; i++) {
        arr.push(buildArray(dims.slice(1)));
      }
      return arr;
    };
    
    return buildArray(shape);
  }

  /**
   * Check if tensors have same shape
   * @param {Tensor} other - Other tensor
   * @returns {boolean} True if same shape
   */
  _sameShape(other) {
    if (this.shape.length !== other.shape.length) {
      return false;
    }
    
    for (let i = 0; i < this.shape.length; i++) {
      if (this.shape[i] !== other.shape[i]) {
        return false;
      }
    }
    
    return true;
  }

  /**
   * Scalar operation
   * @param {number} scalar - Scalar value
   * @param {Function} operation - Operation function
   * @returns {Tensor} Result tensor
   */
  _scalarOperation(scalar, operation) {
    const result = this._deepCopy();
    this._applyScalarOperation(result.data, scalar, operation);
    return new Tensor(result.data, this.shape);
  }

  /**
   * Element-wise operation
   * @param {Tensor} other - Other tensor
   * @param {Function} operation - Operation function
   * @returns {Tensor} Result tensor
   */
  _elementWiseOperation(other, operation) {
    const result = this._deepCopy();
    this._applyElementWiseOperation(result.data, other.data, operation);
    return new Tensor(result.data, this.shape);
  }

  /**
   * Apply scalar operation recursively
   * @param {Array} data - Data array
   * @param {number} scalar - Scalar value
   * @param {Function} operation - Operation function
   */
  _applyScalarOperation(data, scalar, operation) {
    for (let i = 0; i < data.length; i++) {
      if (Array.isArray(data[i])) {
        this._applyScalarOperation(data[i], scalar, operation);
      } else {
        data[i] = operation(data[i], scalar);
      }
    }
  }

  /**
   * Apply element-wise operation recursively
   * @param {Array} data1 - First data array
   * @param {Array} data2 - Second data array
   * @param {Function} operation - Operation function
   */
  _applyElementWiseOperation(data1, data2, operation) {
    for (let i = 0; i < data1.length; i++) {
      if (Array.isArray(data1[i])) {
        this._applyElementWiseOperation(data1[i], data2[i], operation);
      } else {
        data1[i] = operation(data1[i], data2[i]);
      }
    }
  }

  /**
   * Deep copy of data
   * @returns {Array} Deep copied data
   */
  _deepCopy() {
    return JSON.parse(JSON.stringify(this.data));
  }

  /**
   * Get string representation
   * @returns {string} String representation
   */
  toString() {
    return `Tensor(shape=${JSON.stringify(this.shape)}, data=${JSON.stringify(this.data)})`;
  }
}

module.exports = Tensor;
