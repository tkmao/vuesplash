/**
 * クッキーの値を取得する
 * @param {String} searchKey 検索するキー
 * @returns {String} キーに対応する値
 */
export function getCookieValue(searchKey) {
  if (typeof searchKey === 'undefined') {
    return ''
  }

  let val = ''

  document.cookie.split(';').forEach(cookie => {
    const [key, value] = cookie.split('=')
    if (key === searchKey) {
      return val = value
    }
  })

  return val
}

/**
 * 勤務時間を計算する
 * @param {Number} start_hh 開始時間(h)
 * @param {Number} start_mm 開始時間(m)
 * @param {Number} end_hh 終了時間(h)
 * @param {Number} end_mm 終了時間(m)
 * @returns {Number} キーに対応する値
 */
export function getWorktime(start_hh, start_mm, end_hh, end_mm, breaktime, breaktime_mid) {
  let worktime = 0;
  let subtime_hh = 0;
  let subtime_mm = 0;
  start_hh = parseInt(start_hh);
  start_mm = parseInt(start_mm);
  end_hh = parseInt(end_hh);
  end_mm = parseInt(end_mm);

  if (isFinite(start_hh) && isFinite(start_mm) && isFinite(end_hh) && isFinite(end_mm)) {
    if (end_hh >= start_hh) {
      subtime_hh = end_hh - start_hh;
      if (end_mm >= start_mm) {
        subtime_mm = ((end_mm - start_mm) * 5) / 3;
        worktime = parseInt(subtime_hh) + parseInt(subtime_mm) / 100;
        worktime = worktime - breaktime - breaktime_mid;
      } else {
        if (subtime_hh > 0) {
          subtime_mm = ((60 + parseInt(end_mm) - start_mm) * 5) / 3;
          subtime_hh = subtime_hh - 1;
          worktime = parseInt(subtime_hh) + parseInt(subtime_mm) / 100;
          worktime = worktime - breaktime - breaktime_mid;
        }
      }
    }
  }
  return worktime;
}

export const OK = 200
export const CREATED = 201
export const NOT_FOUND = 404
export const UNAUTHORIZED = 419
export const UNPROCESSABLE_ENTITY = 422
export const INTERNAL_SERVER_ERROR = 500
