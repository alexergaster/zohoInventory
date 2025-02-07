import axios from "axios";

const URL = `http://127.0.0.1:8000/api`;

export const getContacts = async () => {
  const response = await axios.get(`${URL}/contacts`);

  return response.data;
};

export const getItems = async () => {
  const response = await axios.get(`${URL}/items`);

  return response.data;
};

export const addContact = async (data) => {
  const response = await axios.post(`${URL}/contacts`, data);

  return response.data;
};
