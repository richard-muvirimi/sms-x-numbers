export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

export interface UploadData {
  id: string;
  original_file: {
    name: string;
    download_url: string;
  };
  chunks: Array<{
    id: string;
    download_url: string;
    size: number;
    index: number;
  }>;
  stats: {
    total: number;
    valid: number;
    invalid: number;
  };
  created_at: string;
  expires_at: string;
  view_url: string;
}

export type UploadResponse = ApiResponse<UploadData>;

export interface Country {
  code: string;
  name: string;
  prefix: string;
}

export type CountriesResponse = ApiResponse<Country[]>;
