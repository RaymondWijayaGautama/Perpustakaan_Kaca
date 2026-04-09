import React from 'react';

const InputField = ({ label, type, identifier, value, onChange }) => {
  return (
    /* Menggunakan Spacing System space-1 (8px) dan space-2 (16px) [cite: 124, 125] */
    <div className="flex flex-col gap-space-1 mb-space-2 text-left">
      
      {/* Label: Menggunakan Roboto Regular dan warna Neutral-900 [cite: 30, 74, 76] */}
      <label 
        htmlFor={identifier} 
        className="font-roboto text-text-high text-[14px] font-medium"
      >
        {label}
      </label>

      {/* Input: Radius-medium (8px) & focus state terlihat sesuai standar Aksesibilitas [cite: 137, 167] */}
      <input
        id={identifier}
        name={identifier}
        type={type}
        value={value}
        onChange={onChange}
        /* Menggunakan token warna brand-primary-500 (#265F9C) untuk ring focus [cite: 30] */
        className="w-full p-space-2 border border-gray-300 rounded-medium font-roboto 
                   focus:outline-none focus:ring-2 focus:ring-brand-primary-500 transition-all"
        placeholder={`Masukkan ${label}...`}
      />
    </div>
  );
};

export default InputField;