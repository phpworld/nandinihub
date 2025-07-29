import React, { createContext, useContext, useReducer, useEffect } from 'react';
import { useAuth } from './AuthContext';
import apiService from '../services/api';
import { API_ENDPOINTS } from '../config/api';

// Initial state
const initialState = {
  items: [],
  totalItems: 0,
  subtotal: 0,
  total: 0,
  isLoading: false,
  error: null,
};

// Action types
const CART_ACTIONS = {
  SET_LOADING: 'SET_LOADING',
  SET_CART: 'SET_CART',
  ADD_ITEM: 'ADD_ITEM',
  UPDATE_ITEM: 'UPDATE_ITEM',
  REMOVE_ITEM: 'REMOVE_ITEM',
  CLEAR_CART: 'CLEAR_CART',
  SET_ERROR: 'SET_ERROR',
  CLEAR_ERROR: 'CLEAR_ERROR',
};

// Reducer
const cartReducer = (state, action) => {
  switch (action.type) {
    case CART_ACTIONS.SET_LOADING:
      return {
        ...state,
        isLoading: action.payload,
      };
    case CART_ACTIONS.SET_CART:
      return {
        ...state,
        ...action.payload,
        isLoading: false,
        error: null,
      };
    case CART_ACTIONS.ADD_ITEM:
      return {
        ...state,
        items: [...state.items, action.payload.item],
        totalItems: action.payload.summary.total_items,
        subtotal: action.payload.summary.subtotal,
        total: action.payload.summary.total,
        isLoading: false,
        error: null,
      };
    case CART_ACTIONS.UPDATE_ITEM:
      return {
        ...state,
        items: state.items.map(item =>
          item.id === action.payload.itemId
            ? { ...item, quantity: action.payload.quantity }
            : item
        ),
        totalItems: action.payload.summary.total_items,
        subtotal: action.payload.summary.subtotal,
        total: action.payload.summary.total,
        isLoading: false,
        error: null,
      };
    case CART_ACTIONS.REMOVE_ITEM:
      return {
        ...state,
        items: state.items.filter(item => item.id !== action.payload.itemId),
        totalItems: action.payload.summary.total_items,
        subtotal: action.payload.summary.subtotal,
        total: action.payload.summary.total,
        isLoading: false,
        error: null,
      };
    case CART_ACTIONS.CLEAR_CART:
      return {
        ...initialState,
        isLoading: false,
      };
    case CART_ACTIONS.SET_ERROR:
      return {
        ...state,
        error: action.payload,
        isLoading: false,
      };
    case CART_ACTIONS.CLEAR_ERROR:
      return {
        ...state,
        error: null,
      };
    default:
      return state;
  }
};

// Create context
const CartContext = createContext();

// Provider component
export const CartProvider = ({ children }) => {
  const [state, dispatch] = useReducer(cartReducer, initialState);
  const { isAuthenticated } = useAuth();

  // Load cart when user is authenticated
  useEffect(() => {
    if (isAuthenticated) {
      loadCart();
    } else {
      dispatch({ type: CART_ACTIONS.CLEAR_CART });
    }
  }, [isAuthenticated]);

  const loadCart = async () => {
    dispatch({ type: CART_ACTIONS.SET_LOADING, payload: true });

    try {
      const response = await apiService.get(API_ENDPOINTS.CART.LIST);
      
      if (response.success) {
        dispatch({
          type: CART_ACTIONS.SET_CART,
          payload: {
            items: response.data.items || [],
            totalItems: response.data.cart_summary?.total_items || 0,
            subtotal: response.data.cart_summary?.subtotal || 0,
            total: response.data.cart_summary?.total || 0,
          },
        });
      } else {
        dispatch({ type: CART_ACTIONS.SET_ERROR, payload: response.message });
      }
    } catch (error) {
      dispatch({ type: CART_ACTIONS.SET_ERROR, payload: error.message });
    }
  };

  const addToCart = async (productId, quantity = 1) => {
    dispatch({ type: CART_ACTIONS.SET_LOADING, payload: true });
    dispatch({ type: CART_ACTIONS.CLEAR_ERROR });

    try {
      const response = await apiService.post(API_ENDPOINTS.CART.ADD, {
        product_id: productId,
        quantity,
      });

      if (response.success) {
        // Reload cart to get updated data
        await loadCart();
        return { success: true };
      } else {
        dispatch({ type: CART_ACTIONS.SET_ERROR, payload: response.message });
        return { success: false, message: response.message };
      }
    } catch (error) {
      const errorMessage = error.message || 'Failed to add item to cart';
      dispatch({ type: CART_ACTIONS.SET_ERROR, payload: errorMessage });
      return { success: false, message: errorMessage };
    }
  };

  const updateCartItem = async (itemId, quantity) => {
    dispatch({ type: CART_ACTIONS.SET_LOADING, payload: true });

    try {
      const response = await apiService.put(`${API_ENDPOINTS.CART.UPDATE}/${itemId}`, {
        quantity,
      });

      if (response.success) {
        dispatch({
          type: CART_ACTIONS.UPDATE_ITEM,
          payload: {
            itemId,
            quantity,
            summary: response.data.cart_summary,
          },
        });
        return { success: true };
      } else {
        dispatch({ type: CART_ACTIONS.SET_ERROR, payload: response.message });
        return { success: false, message: response.message };
      }
    } catch (error) {
      const errorMessage = error.message || 'Failed to update cart item';
      dispatch({ type: CART_ACTIONS.SET_ERROR, payload: errorMessage });
      return { success: false, message: errorMessage };
    }
  };

  const removeFromCart = async (itemId) => {
    dispatch({ type: CART_ACTIONS.SET_LOADING, payload: true });

    try {
      const response = await apiService.delete(`${API_ENDPOINTS.CART.REMOVE}/${itemId}`);

      if (response.success) {
        dispatch({
          type: CART_ACTIONS.REMOVE_ITEM,
          payload: {
            itemId,
            summary: response.data.cart_summary,
          },
        });
        return { success: true };
      } else {
        dispatch({ type: CART_ACTIONS.SET_ERROR, payload: response.message });
        return { success: false, message: response.message };
      }
    } catch (error) {
      const errorMessage = error.message || 'Failed to remove item from cart';
      dispatch({ type: CART_ACTIONS.SET_ERROR, payload: errorMessage });
      return { success: false, message: errorMessage };
    }
  };

  const clearCart = async () => {
    dispatch({ type: CART_ACTIONS.SET_LOADING, payload: true });

    try {
      const response = await apiService.delete(API_ENDPOINTS.CART.CLEAR);

      if (response.success) {
        dispatch({ type: CART_ACTIONS.CLEAR_CART });
        return { success: true };
      } else {
        dispatch({ type: CART_ACTIONS.SET_ERROR, payload: response.message });
        return { success: false, message: response.message };
      }
    } catch (error) {
      const errorMessage = error.message || 'Failed to clear cart';
      dispatch({ type: CART_ACTIONS.SET_ERROR, payload: errorMessage });
      return { success: false, message: errorMessage };
    }
  };

  const clearError = () => {
    dispatch({ type: CART_ACTIONS.CLEAR_ERROR });
  };

  const value = {
    ...state,
    loadCart,
    addToCart,
    updateCartItem,
    removeFromCart,
    clearCart,
    clearError,
  };

  return (
    <CartContext.Provider value={value}>
      {children}
    </CartContext.Provider>
  );
};

// Hook to use cart context
export const useCart = () => {
  const context = useContext(CartContext);
  if (!context) {
    throw new Error('useCart must be used within a CartProvider');
  }
  return context;
};
